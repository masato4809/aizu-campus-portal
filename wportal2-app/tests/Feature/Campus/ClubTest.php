<?php

declare(strict_types=1);

namespace Tests\Feature\Campus;

use App\Campus\ClubFeed;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClubTest extends CampusTestCase
{
    private function member(string $email, string $circle, ?string $type = 'AAA', string $university = '会津大学'): int
    {
        $id = $this->student($email);
        DB::table('campus_students')->where('id', $id)->update(['circle' => $circle, 'personality' => $type, 'university' => $university]);

        return $id;
    }

    private function review(int $student, string $date = '2026-09-01 00:00:00', string $university = '会津大学'): int
    {
        $course = DB::table('campus_courses')->insertGetId([
            'university' => $university, 'name' => '授業'.DB::table('campus_courses')->count(), 'professor' => '教授',
            'created_at' => $date, 'updated_at' => $date,
        ]);

        return DB::table('campus_reviews')->insertGetId([
            'student_id' => $student, 'course_id' => $course, 'body' => '参考になる口コミ',
            'created_at' => $date, 'updated_at' => $date,
        ]);
    }

    /** @return Collection<int, \stdClass> */
    private function ranking(int $student, string $tab = 'reviews'): Collection
    {
        $me = DB::table('campus_students')->where('id', $student)->first();
        $this->assertNotNull($me);

        return app(ClubFeed::class)->get($me, $tab)
            ->filter(fn ($club) => $club->member_count > 0 || $club->review_count > 0 || $club->posts->isNotEmpty())
            ->values();
    }

    public function test_review_ranking_groups_posts_without_multiplying_counts_and_is_university_scoped(): void
    {
        $music    = $this->member('music@example.test', 'CUO');
        $second   = $this->member('second@example.test', 'CUO', 'AAB');
        $sport    = $this->member('sport@example.test', 'サッカー部');
        $outsider = $this->member('outside@example.test', 'CUO', 'AAA', '別大学');
        $none     = $this->member('none@example.test', '');
        $this->review($music);
        $this->review($second);
        $this->review($sport, '2026-09-02 00:00:00');
        $this->review($outsider);
        $this->review($music, university: '別大学');
        $this->review($none);
        $this->withSession(['campus_student_id' => $music]);
        foreach (['最初のCUOの募集です。', '新しいCUOの募集です。'] as $body) {
            $this->post('/campus/club', ['name' => 'CUO', 'body' => $body])->assertSessionHasNoErrors();
        }
        $clubs    = $this->ranking($music);
        $this->assertSame(['CUO', 'サッカー部'], $clubs->pluck('name')->all());
        $first    = $clubs->get(0);
        $this->assertNotNull($first);
        $this->assertSame(2, $first->review_count);
        $this->assertSame(2, $first->member_count);
        $this->assertSame(2, $first->diagnosed_count);
        $this->assertCount(2, $first->posts);
        $this->get('/campus/clubs')->assertOk()->assertSee('口コミ投稿数ランキング')->assertSee('以前の活動・募集情報（1件）');
    }

    public function test_review_ties_use_original_submission_time_and_deletions_are_recounted(): void
    {
        $a      = $this->member('a@example.test', 'ARC');
        $b      = $this->member('b@example.test', 'REMs');
        $review = $this->review($a);
        $newer  = $this->review($b, '2026-09-02 00:00:00');
        DB::table('campus_reviews')->where('id', $review)->update(['body' => '編集した口コミ', 'updated_at' => now()]);
        $this->assertSame(['REMs', 'ARC'], $this->ranking($a)->pluck('name')->all());
        $this->assertSame([1, 1], $this->ranking($a)->pluck('review_count')->all());
        DB::table('campus_reviews')->where('id', $newer)->delete();
        $this->assertSame(['ARC', 'REMs'], $this->ranking($a)->pluck('name')->all());
        $this->assertSame([1, 0], $this->ranking($a)->pluck('review_count')->all());
    }

    public function test_similarity_uses_diagnosed_members_and_places_missing_results_last(): void
    {
        $me    = $this->member('me@example.test', 'CUO');
        $this->member('different@example.test', 'CUO', 'AAB');
        $this->member('unknown@example.test', 'CUO', null);
        $same  = $this->member('same@example.test', 'DMC');
        $this->member('zero@example.test', 'REMs', 'AAB');
        $this->member('missing@example.test', 'Zli', null);
        $this->review($me);
        $clubs = $this->ranking($me, 'similarity');
        $this->assertSame(['DMC', 'CUO', 'REMs', 'Zli'], $clubs->pluck('name')->all());
        $this->assertEquals([100, 50, 0, null], $clubs->pluck('similarity')->all());
        $half  = $clubs->get(1);
        $this->assertNotNull($half);
        $this->assertSame(3, $half->member_count);
        $this->assertSame(2, $half->diagnosed_count);
        $this->assertSame(1, $half->matching_count);
        $this->assertEquals([50, 50], $half->distribution->pluck('percentage')->all());
        $this->withSession(['campus_student_id' => $me])->get('/campus/clubs?tab=similarity')
            ->assertOk()->assertSee('あなたのタイプ：AAA')->assertSee('未登録 1人');
        $this->get('/campus/clubs')->assertViewHas('clubs', fn ($rows) => $rows->first()->name === 'CUO');

        // A profile change updates both memberships and stored result aggregates immediately.
        $this->withSession(['campus_student_id' => $same])->post('/campus/profile', [
            'name' => '変更したメンバー', 'faculty' => '理工学域', 'year' => 1, 'circle' => 'CUO', 'personality' => 'AAB',
        ])->assertSessionHasNoErrors();
        $half  = $this->ranking($me)->firstWhere('name', 'CUO');
        $this->assertNotNull($half);
        $this->assertSame(4, $half->member_count);
        $this->assertSame(3, $half->diagnosed_count);
        $this->assertEqualsWithDelta(200 / 3, $half->similarity, 0.001);
        $this->assertDatabaseHas('campus_students', ['id' => $same, 'personality' => 'AAA']);
        $this->withSession(['campus_student_id' => $me])->post('/campus/profile', [
            'name' => '自分', 'faculty' => '理工学域', 'year' => 1, 'circle' => 'ARC', 'personality' => '',
        ])->assertSessionHasNoErrors();
        $arc   = $this->ranking($me)->firstWhere('name', 'ARC');
        $this->assertNotNull($arc);
        $this->assertSame(1, $arc->review_count);
        $cuo   = $this->ranking($me)->firstWhere('name', 'CUO');
        $this->assertNotNull($cuo);
        $this->assertSame(0, $cuo->review_count);
        $this->get('/campus/clubs?tab=similarity')->assertOk()->assertSee('あなたのタイプ：AAA');
    }

    public function test_unregistered_clubs_are_rejected_and_catalog_edit_requires_password(): void
    {
        $student = $this->student('editor@example.test');
        $this->withSession(['campus_student_id' => $student])
            ->post('/campus/club', ['name' => '新しいサークル', 'body' => '未登録サークルの投稿です。'])
            ->assertSessionHasErrors('name');

        $catalog = DB::table('campus_club_catalog')->where('name', 'CUO')->first();
        $this->assertNotNull($catalog);
        $this->put('/campus/club-catalog/'.$catalog->id, [
            'category' => '文化サークル',
            'tags'     => '音楽、ライブ',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('password');

        config(['campus.club_edit_password' => 'correct-password']);
        $this->put('/campus/club-catalog/'.$catalog->id, [
            'category' => '文化サークル',
            'tags'     => '音楽、ライブ',
            'password' => 'correct-password',
        ])->assertRedirect('/campus/clubs');
        $this->assertDatabaseHas('campus_club_catalog', [
            'id'   => $catalog->id,
            'tags' => json_encode(['音楽', 'ライブ'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }

    public function test_catalog_images_can_be_registered_replaced_and_served_to_the_same_university(): void
    {
        Storage::fake('campus_clubs');
        $owner    = $this->student('catalog-image-owner@example.test');
        $other    = $this->student('catalog-image-other@example.test');
        $outsider = $this->student('catalog-image-outsider@example.test');
        DB::table('campus_students')->where('id', $outsider)->update(['university' => '別大学']);
        $catalog  = DB::table('campus_club_catalog')->where('name', 'CUO')->first();
        $this->assertNotNull($catalog);
        config(['campus.club_edit_password' => 'password00']);

        $this->withSession(['campus_student_id' => $owner])->put('/campus/club-catalog/'.$catalog->id, [
            'category' => '文化サークル', 'tags' => '音楽', 'password' => 'password00',
            'image'    => UploadedFile::fake()->image('cuo.jpg', 2400, 1200),
        ])->assertSessionHasNoErrors()->assertRedirect('/campus/clubs');
        $saved    = DB::table('campus_club_catalog')->where('id', $catalog->id)->first();
        $this->assertNotNull($saved);
        $this->assertNotNull($saved->image_path);
        $this->assertSame([1600, 800], [(int) $saved->image_width, (int) $saved->image_height]);
        Storage::disk('campus_clubs')->assertExists($saved->image_path);
        $this->withSession(['campus_student_id' => $other])->get('/campus/club-catalog-images/'.$catalog->id)
            ->assertOk()->assertHeader('Content-Type', 'image/jpeg')->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->withSession(['campus_student_id' => $outsider])->get('/campus/club-catalog-images/'.$catalog->id)->assertNotFound();

        $oldPath  = $saved->image_path;
        $this->withSession(['campus_student_id' => $owner])->put('/campus/club-catalog/'.$catalog->id, [
            'category' => '文化サークル', 'tags' => '音楽', 'password' => 'password00',
            'image'    => UploadedFile::fake()->image('cuo-new.png', 400, 800),
        ])->assertSessionHasNoErrors()->assertRedirect('/campus/clubs');
        $replaced = DB::table('campus_club_catalog')->where('id', $catalog->id)->first();
        $this->assertNotNull($replaced);
        $this->assertNotSame($oldPath, $replaced->image_path);
        $this->assertSame([400, 800], [(int) $replaced->image_width, (int) $replaced->image_height]);
        Storage::disk('campus_clubs')->assertMissing($oldPath);
        Storage::disk('campus_clubs')->assertExists($replaced->image_path);
        $this->withSession(['campus_student_id' => $owner])->get('/campus/clubs')
            ->assertOk()->assertSee('/campus/club-catalog-images/'.$catalog->id);
    }

    public function test_no_members_or_results_and_empty_rankings_render_without_division_by_zero(): void
    {
        $me   = $this->member('me@example.test', '', 'AAA');
        $this->withSession(['campus_student_id' => $me])->get('/campus/clubs')->assertOk()->assertSee('ARC');
        $this->post('/campus/club', ['name' => 'ARC', 'body' => 'ARCの新しい募集情報です。'])->assertSessionHasNoErrors();
        $club = $this->ranking($me)->first();
        $this->assertNotNull($club);
        $this->assertSame(0, $club->member_count);
        $this->assertSame(0, $club->review_count);
        $this->assertNull($club->similarity);
        $this->get('/campus/clubs?tab=invalid')->assertOk()->assertViewHas('clubTab', 'reviews');
    }

    public function test_images_are_resized_served_privately_and_deleted_only_by_the_author(): void
    {
        Storage::fake('campus_clubs');
        $owner      = $this->member('owner@example.test', 'フォトサークル');
        $other      = $this->member('other@example.test', 'フォトサークル');
        $outsider   = $this->member('outsider@example.test', 'フォトサークル', university: '別大学');
        $this->withSession(['campus_student_id' => $owner])->post('/campus/club', [
            'name'   => 'フォトサークル', 'body' => 'フォトサークルの活動の紹介です。', 'student_id' => $other,
            'images' => [UploadedFile::fake()->image('photo.jpg', 2400, 1200), UploadedFile::fake()->image('photo.png', 400, 800), UploadedFile::fake()->image('photo.webp', 300, 300)],
        ])->assertSessionHasNoErrors()->assertRedirect('/campus/clubs');
        $club       = DB::table('campus_clubs')->first();
        $this->assertNotNull($club);
        $this->assertSame($owner, $club->student_id);
        $images     = DB::table('campus_club_images')->orderBy('id')->get();
        $this->assertCount(3, $images);
        $firstImage = $images->first();
        $this->assertNotNull($firstImage);
        $this->assertSame([1600, 400, 300], $images->pluck('width')->all());
        $this->assertSame([800, 800, 300], $images->pluck('height')->all());
        foreach ($images as $image) {
            $bytes = Storage::disk('campus_clubs')->get($image->path);
            $this->assertIsString($bytes);
            $size  = getimagesizefromstring($bytes);
            $this->assertNotFalse($size);
            $this->assertSame([$image->width, $image->height], [$size[0], $size[1]]);
            $this->assertSame($image->mime_type, $size['mime']);
        }
        $this->get('/campus/clubs')->assertOk()->assertSee('/campus/club-images/'.$firstImage->id)->assertSee('この投稿を削除');
        $this->withSession(['campus_student_id' => $other])->get('/campus/club-images/'.$firstImage->id)
            ->assertOk()->assertHeader('Content-Type', 'image/jpeg')->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->delete('/campus/club/'.$club->id)->assertNotFound();
        $this->assertDatabaseHas('campus_clubs', ['id' => $club->id]);
        $this->withSession(['campus_student_id' => $outsider])->get('/campus/club-images/'.$firstImage->id)->assertNotFound();
        $this->get('/campus/clubs')->assertDontSee('フォトサークルの活動の紹介です。');
        $this->withSession(['campus_student_id' => null])->get('/campus/club-images/'.$firstImage->id)->assertRedirect('/campus/login');
        $this->delete('/campus/club/'.$club->id)->assertRedirect('/campus/login');
        $this->withSession(['campus_student_id' => $owner])->delete('/campus/club/'.$club->id)->assertRedirect('/campus/clubs');
        $this->assertDatabaseCount('campus_club_images', 0);
        $this->assertDatabaseCount('campus_clubs', 0);
        $this->assertSame([], Storage::disk('campus_clubs')->allFiles());
        $this->get('/campus/club-images/'.$firstImage->id)->assertNotFound();
    }

    public function test_image_limits_and_invalid_formats_leave_no_posts_or_files(): void
    {
        Storage::fake('campus_clubs');
        $this->withSession(['campus_student_id' => $this->student('owner@example.test')]);
        $data = ['name' => 'フォトサークル', 'body' => '画像付きの活動情報を投稿します。'];
        $this->post('/campus/club', $data + ['images' => array_map(fn () => UploadedFile::fake()->image('photo.jpg'), range(1, 5))])->assertSessionHasErrors('images');
        $this->post('/campus/club', $data + ['images' => [UploadedFile::fake()->image('photo.jpg')->size(5121)]])->assertSessionHasErrors('images.0');
        $this->post('/campus/club', $data + ['images' => [UploadedFile::fake()->createWithContent('fake.jpg', 'not an image')]])->assertSessionHasErrors('images.0');
        $this->post('/campus/club', $data + ['images' => [UploadedFile::fake()->image('photo.gif')]])->assertSessionHasErrors('images.0');
        $this->post('/campus/club', $data + ['images' => [UploadedFile::fake()->createWithContent('photo.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>')]])->assertSessionHasErrors('images.0');
        $this->assertDatabaseCount('campus_clubs', 0);
        $this->assertSame([], Storage::disk('campus_clubs')->allFiles());
        $this->post('/campus/club', $data + ['images' => array_map(fn () => UploadedFile::fake()->image('photo.jpg')->size(5120), range(1, 4))])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('campus_club_images', 4);
    }

    public function test_failed_later_image_or_database_write_cleans_up_earlier_files(): void
    {
        Storage::fake('campus_clubs');
        $this->withSession(['campus_student_id' => $this->student('owner@example.test')]);
        $data = ['name' => 'フォトサークル', 'body' => '画像付きの活動情報を投稿します。'];
        $this->post('/campus/club', $data + ['images' => [
            UploadedFile::fake()->image('valid.jpg'), UploadedFile::fake()->image('huge.png', 4001, 4000),
        ]])->assertSessionHasErrors('images.1');
        $this->assertDatabaseCount('campus_clubs', 0);
        $this->assertSame([], Storage::disk('campus_clubs')->allFiles());

        DB::statement("CREATE TRIGGER fail_club_image BEFORE INSERT ON campus_club_images BEGIN SELECT RAISE(ABORT, 'test failure'); END");
        $this->post('/campus/club', $data + ['images' => [UploadedFile::fake()->image('valid.jpg')]])->assertStatus(500);
        $this->assertDatabaseCount('campus_clubs', 0);
        $this->assertDatabaseCount('campus_club_images', 0);
        $this->assertSame([], Storage::disk('campus_clubs')->allFiles());
    }
}

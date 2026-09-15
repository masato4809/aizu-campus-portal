<?php

declare(strict_types=1);

namespace Tests\Feature\Campus;

use App\Campus\StudentMatches;
use Illuminate\Support\Facades\DB;

class StudentMatchesTest extends CampusTestCase
{
    public function test_card_counts_are_visible_without_expanding_and_matching_is_preserved(): void
    {
        $viewer          = $this->student('viewer@example.test', 1);
        $first           = $this->student('first@example.test', 2);
        $second          = $this->student('second@example.test', 4);
        $sameYear        = $this->student('same@example.test', 1);
        $otherUniversity = $this->student('other@example.test', 3);
        DB::table('campus_students')->where('id', $otherUniversity)->update(['university' => '別大学']);
        DB::table('campus_students')->where('id', $second)->update(['past_exam_count' => 12, 'circle' => '', 'personality' => null, 'goal_orientation' => null]);
        foreach ([$first => 3, $second => 7] as $author => $count) {
            for ($i = 0; $i < $count; $i++) {
                $course = DB::table('campus_courses')->insertGetId(['university' => '会津大学', 'name' => '授業'.$author.'-'.$i, 'professor' => '先生']);
                DB::table('campus_reviews')->insert(['student_id' => $author, 'course_id' => $course, 'body' => '口コミ']);
            }
        }
        DB::table('campus_students')->whereIn('id', [$viewer, $first])->update(['personality' => 'AAA', 'goal_orientation' => 'high_grade']);
        $me              = DB::table('campus_students')->where('id', $viewer)->first();
        $this->assertNotNull($me);
        DB::enableQueryLog();
        $matches         = app(StudentMatches::class)->get($me);
        $this->assertCount(1, DB::getQueryLog());
        DB::disableQueryLog();
        $this->assertSame([$first, $second], $matches->pluck('id')->values()->all());
        $this->assertNotNull($matches->first());
        $this->assertNotNull($matches->last());
        $this->assertSame(100, $matches->first()->score);
        $this->assertSame(15, $matches->last()->score);
        $this->assertSame(12, (int) $matches->last()->past_exam_count);
        $this->assertNotContains($sameYear, $matches->pluck('id')->all());
        $response        = $this->withSession(['campus_student_id' => $viewer])->get('/campus/matches')->assertOk()
            ->assertSee('投稿 3件')->assertSee('投稿 7件')->assertSee('過去問なし')->assertSee('過去問 12問所持')->assertDontSee('other@example.test');
        $html            = $response->getContent();
        $this->assertIsString($html);
        preg_match_all('/<article class="card">(.*?)<details>/s', $html, $cards);
        $this->assertCount(2, $cards[1]);
        $this->assertStringContainsString('投稿 3件', $cards[1][0]);
        $this->assertStringContainsString('過去問なし', $cards[1][0]);
        $this->assertStringContainsString('投稿 7件', $cards[1][1]);
        $this->assertStringContainsString('過去問 12問所持', $cards[1][1]);
        $me->year        = 4;
        $this->assertCount(0, app(StudentMatches::class)->get($me));
    }

    public function test_partial_personality_and_balanced_answers_get_partial_points(): void
    {
        $viewer = $this->student('partial-viewer@example.test', 1);
        $senior = $this->student('partial-senior@example.test', 2);
        DB::table('campus_students')->where('id', $viewer)->update(['personality' => 'AAA', 'goal_orientation' => 'high_grade', 'circle' => '']);
        DB::table('campus_students')->where('id', $senior)->update(['personality' => 'AAB', 'goal_orientation' => 'balanced', 'circle' => '']);
        $me     = DB::table('campus_students')->where('id', $viewer)->first();
        $this->assertNotNull($me);
        $match  = app(StudentMatches::class)->get($me)->first();
        $this->assertNotNull($match);
        $this->assertSame(65, $match->score);
        $this->assertContains('性格の2/3軸が共通（+40）', $match->reasons);
        $this->assertContains('学習目標が近い（+10）', $match->reasons);
    }
}

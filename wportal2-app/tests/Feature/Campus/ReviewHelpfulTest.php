<?php

declare(strict_types=1);

namespace Tests\Feature\Campus;

use Illuminate\Support\Facades\DB;

class ReviewHelpfulTest extends CampusTestCase
{
    public function test_votes_persist_are_idempotent_and_can_be_removed(): void
    {
        $author = $this->student('author@example.test');
        $voter  = $this->student('voter@example.test');
        $course = DB::table('campus_courses')->insertGetId(['university' => '会津大学', 'name' => '投票授業', 'professor' => '先生']);
        $review = DB::table('campus_reviews')->insertGetId(['student_id' => $author, 'course_id' => $course, 'body' => '役立つコメント']);
        DB::table('campus_students')->where('id', $author)->update(['circle' => '投稿者のサークル']);
        DB::table('campus_students')->where('id', $voter)->update(['circle' => '']);
        $url    = '/campus/reviews/'.$review.'/helpful';
        $this->post($url, ['helpful' => 1])->assertRedirect('/campus/login');
        $this->withSession(['campus_student_id' => $author])->postJson($url, ['helpful' => 1])->assertForbidden();
        $this->withSession(['campus_student_id' => $voter])->postJson($url, ['helpful' => 2])->assertUnprocessable();
        foreach ([1, 1] as $value) {
            $this->postJson($url, ['helpful' => $value, 'student_id' => $author])->assertOk()->assertJsonPath('helpful_count', 1);
        }
        $this->assertDatabaseHas('campus_review_helpful_votes', ['student_id' => $voter, 'review_id' => $review]);
        $this->get('/campus/courses/'.$course)->assertOk()->assertSee('投稿者のサークル')->assertSee('役に立った：1件')->assertSee('役に立ったを取り消す');
        $this->withSession(['campus_student_id' => $this->student('another@example.test')]);
        $this->post($url, ['helpful' => 1])->assertRedirect('/campus/courses/'.$course.'#review-'.$review);
        $this->withSession(['campus_student_id' => $voter]);
        foreach ([0, 0] as $value) {
            $this->postJson($url, ['helpful' => $value])->assertOk()->assertJsonPath('helpful_count', 1)->assertJsonPath('helpful', false);
        }
        $this->get('/campus/courses/'.$course)->assertOk()->assertDontSee('役に立ったを取り消す');
        DB::table('campus_courses')->where('id', $course)->update(['university' => 'Other']);
        $this->postJson($url, ['helpful' => 1])->assertNotFound();
        $this->postJson('/campus/reviews/999999/helpful', ['helpful' => 1])->assertNotFound();
        DB::table('campus_reviews')->where('id', $review)->delete();
        $this->assertDatabaseCount('campus_review_helpful_votes', 0);
    }
}

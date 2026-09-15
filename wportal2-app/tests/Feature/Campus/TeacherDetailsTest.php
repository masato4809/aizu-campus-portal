<?php

declare(strict_types=1);

namespace Tests\Feature\Campus;

use App\Campus\TeacherDetails;
use Illuminate\Support\Facades\DB;

class TeacherDetailsTest extends CampusTestCase
{
    public function test_details_aggregate_each_review_once_and_exclude_coordinator_only_courses(): void
    {
        $studentId = $this->student('teacher-review@example.test');
        $student   = DB::table('campus_students')->where('id', $studentId)->first();
        $this->assertNotNull($student);
        $teacher   = DB::table('campus_teachers')->insertGetId(['university' => '会津大学', 'source_key' => 'details-test', 'name' => '先生', 'profile_url' => '', 'laboratory_name' => '研究室']);
        $courses   = [];
        foreach (['PL01', 'PL02', 'PL03'] as $code) {
            $courses[] = DB::table('campus_courses')->insertGetId(['university' => '会津大学', 'academic_year' => 2026, 'course_code' => $code, 'name' => $code, 'professor' => '先生']);
        }
        foreach ([[$courses[0], 'instructor'], [$courses[0], 'coordinator'], [$courses[1], 'instructor'], [$courses[2], 'coordinator']] as [$course, $role]) {
            DB::table('campus_course_teachers')->insert(['course_id' => $course, 'teacher_id' => $teacher, 'role' => $role, 'match_method' => 'official_id']);
        }
        foreach ([[$courses[0], 2], [$courses[1], 4], [$courses[2], 1]] as [$course, $rating]) {
            DB::table('campus_reviews')->insert(['student_id' => $studentId, 'course_id' => $course, 'body' => '', 'satisfaction' => $rating]);
        }
        $details   = app(TeacherDetails::class)->get($student, $teacher);
        $this->assertCount(2, $details['courses']);
        $this->assertSame('研究室', $details['laboratory']['name']);
        $this->assertSame(3.0, $details['course_review_averages']['satisfaction']);
        $this->assertSame(2, $details['course_review_count']);
        $this->assertNull($details['course_review_averages']['credit_ease']);
        $this->assertSame(0, $details['course_review_rating_counts']['credit_ease']);
    }

    public function test_student_teacher_review_validation_update_and_university_access(): void
    {
        $id      = $this->student('author@example.test');
        $teacher = DB::table('campus_teachers')->insertGetId(['university' => '会津大学', 'source_key' => 'post-test', 'name' => '先生', 'profile_url' => '']);
        $url     = '/campus/teachers/'.$teacher;
        $this->postJson($url.'/reviews', ['rating' => 5, 'body' => '説明が丁寧'])->assertRedirect('/campus/login');
        $this->withSession(['campus_student_id' => $id]);
        $this->postJson($url.'/reviews', ['rating' => 6, 'body' => str_repeat('あ', 501)])->assertUnprocessable()->assertJsonValidationErrors(['rating', 'body']);
        $this->postJson($url.'/reviews', ['rating' => 5, 'body' => '説明が丁寧', 'student_id' => 999])->assertOk();
        $this->postJson($url.'/reviews', ['rating' => 3, 'body' => '更新'])->assertOk();
        $this->assertDatabaseCount('campus_teacher_reviews', 1);
        $this->assertDatabaseHas('campus_teacher_reviews', ['teacher_id' => $teacher, 'student_id' => $id, 'rating' => 3, 'body' => '更新']);
        $this->getJson($url)->assertOk()->assertJsonPath('teacher_review_count', 1)->assertJsonPath('comments.data.0.body', '更新');
        $this->withSession(['campus_student_id' => $this->student('second@example.test')]);
        $this->postJson($url.'/reviews', ['rating' => 5, 'body' => '別の学生'])->assertOk();
        $this->getJson($url)->assertOk()->assertJsonPath('teacher_rating_average', 4)->assertJsonPath('teacher_review_count', 2);
        DB::table('campus_teachers')->where('id', $teacher)->update(['university' => 'Other']);
        $this->getJson($url)->assertNotFound();
        $this->postJson($url.'/reviews', ['rating' => 5, 'body' => '不可'])->assertNotFound();
    }

    public function test_professor_pages_and_course_links_use_database_ids(): void
    {
        $teacher = DB::table('campus_teachers')->insertGetId(['university' => '会津大学', 'source_key' => 'page-test', 'name' => '詳細先生', 'name_en' => 'DETAIL Teacher', 'profile_url' => '', 'laboratory_name' => '情報研究室']);
        $course  = DB::table('campus_courses')->insertGetId(['university' => '会津大学', 'academic_year' => 2026, 'course_code' => 'PL01', 'name' => '詳細授業', 'professor' => '詳細先生']);
        DB::table('campus_course_teachers')->insert(['course_id' => $course, 'teacher_id' => $teacher, 'role' => 'instructor', 'match_method' => 'official_id']);
        $this->get('/campus/professors/'.$teacher)->assertRedirect('/campus/login');
        $this->withSession(['campus_student_id' => $this->student('pages@example.test')]);
        $this->get('/campus/professors?q=DETAIL')->assertOk()->assertSee('詳細先生')->assertSee('/campus/professors/'.$teacher, false);
        $this->get('/campus/professors/'.$teacher)->assertOk()->assertDontSee('情報研究室')->assertSee('未評価')->assertSee('/campus/courses/'.$course, false);
        $this->get('/campus/courses/'.$course)->assertOk()->assertSee('詳細授業')->assertSee('/campus/professors/'.$teacher, false);
        $this->post('/campus/teachers/'.$teacher.'/reviews', ['rating' => 4, 'body' => '<script>alert(1)</script>'])
            ->assertRedirect('/campus/professors/'.$teacher);
        $this->get('/campus/professors/'.$teacher)->assertOk()->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        DB::table('campus_courses')->where('id', $course)->update(['university' => 'Other']);
        DB::table('campus_teachers')->where('id', $teacher)->update(['university' => 'Other']);
        $this->get('/campus/courses/'.$course)->assertNotFound();
        $this->get('/campus/professors/'.$teacher)->assertNotFound();
    }

    public function test_student_public_profile_hides_private_fields_and_enforces_university(): void
    {
        $viewer = $this->student('viewer-profile@example.test');
        $author = $this->student('private-author@example.test');
        DB::table('campus_students')->where('id', $author)->update(['name' => '公開する名前']);
        $this->withSession(['campus_student_id' => $viewer])->get('/campus/students/'.$author)->assertOk()
            ->assertSee('公開する名前')->assertSee('この人の授業投稿')->assertDontSee('private-author@example.test');
        DB::table('campus_students')->where('id', $author)->update(['university' => '別大学']);
        $this->get('/campus/students/'.$author)->assertNotFound();
    }
}

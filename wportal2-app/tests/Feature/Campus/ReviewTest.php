<?php

declare(strict_types=1);

namespace Tests\Feature\Campus;

use App\Campus\CampusOptions;
use App\Campus\CourseCatalog;
use Illuminate\Support\Facades\DB;

class ReviewTest extends CampusTestCase
{
    /** @return array<string, mixed> */
    private function payload(): array
    {
        return [
            'course'               => '情報科学入門', 'professor' => '先生', 'faculty' => 'コンピュータ理工学部',
            'department'           => 'コンピュータ理工学科', 'user_type' => 'gpa', 'has_past_exam' => 0,
            'test_weight'          => 60, 'report_weight' => 40, 'attendance_weight' => 0,
            'assignment_load'      => 'few', 'remote_level' => 'none',
            'materials'            => '配布資料',
            'class_size'           => 'range_20_39', 'academic_year' => 2025, 'semester' => 'second',
            'body'                 => str_repeat('あ', 500), 'credit_ease' => 1, 'grade_ease' => 2,
            'assignment_lightness' => 3, 'satisfaction' => 4, 'past_exam_similarity' => 5,
        ];
    }

    public function test_submission_search_and_update(): void
    {
        $id      = $this->student('author@example.test');
        $data    = $this->payload();
        $this->withSession(['campus_student_id' => $id])->post('/campus/review', $data)->assertSessionHasNoErrors()->assertRedirect('/campus/review/thanks');
        $this->get('/campus/review/thanks')->assertOk()->assertSee('ありがとうございました')->assertSee('投稿した授業を見る');
        $review  = DB::table('campus_reviews')->first();
        $this->assertNotNull($review);
        $this->assertSame('author@example.test', $review->username);
        $this->assertSame(0, $review->has_past_exam);
        $this->assertSame(500, mb_strlen($review->body));
        $this->assertNull($review->clarity);
        $this->assertDatabaseHas('campus_reviews', ['user_type' => 'gpa', 'academic_year' => 2025, 'semester' => 'second', 'class_size' => 'range_20_39', 'assignment_load' => 'few', 'remote_level' => 'none', 'test_weight' => 60, 'report_weight' => 40]);
        $student = DB::table('campus_students')->where('id', $id)->first();
        $this->assertNotNull($student);
        $catalog = app(CourseCatalog::class)->get($student, '情報');
        $this->assertCount(1, $catalog['courses']);
        $this->assertCount(1, app(CourseCatalog::class)->get($student, '先生')['courses']);
        $this->assertCount(0, app(CourseCatalog::class)->get($student, '該当なし')['courses']);
        DB::table('campus_reviews')->where('id', $review->id)->update(['created_at' => '2020-01-01 00:00:00']);
        $this->post('/campus/review', array_replace($data, ['satisfaction' => 5]))->assertSessionHasNoErrors();
        $this->assertSame(1, DB::table('campus_reviews')->count());
        $this->assertDatabaseHas('campus_reviews', ['id' => $review->id, 'satisfaction' => 5, 'created_at' => '2020-01-01 00:00:00']);
    }

    public function test_validation_rejects_invalid_values(): void
    {
        $this->withSession(['campus_student_id' => $this->student('student@example.test')]);
        $invalid = [
            'user_type'            => 'unknown', 'has_past_exam' => 2, 'faculty' => '別学部', 'department' => '別学科',
            'test_weight'          => 70, 'report_weight' => 70, 'assignment_load' => 'normal', 'remote_level' => 100,
            'class_size'           => 'unknown', 'academic_year' => now()->year + 1,
            'semester'             => 'summer', 'body' => str_repeat('あ', 501), 'credit_ease' => 0, 'grade_ease' => 6,
            'assignment_lightness' => 1.5, 'satisfaction' => null, 'past_exam_similarity' => 'invalid',
        ];
        $this->post('/campus/review', array_replace($this->payload(), $invalid))
            ->assertSessionHasErrors(['user_type', 'has_past_exam', 'faculty', 'department', 'test_weight', 'assignment_load', 'remote_level', 'class_size', 'academic_year', 'semester', 'body', 'credit_ease', 'grade_ease', 'assignment_lightness', 'satisfaction', 'past_exam_similarity']);
        $this->assertSame(0, DB::table('campus_reviews')->count());
        $this->post('/campus/review', array_replace($this->payload(), ['test_weight' => 0, 'report_weight' => 0, 'attendance_weight' => 0, 'body' => '']))->assertSessionHasErrors(['test_weight', 'body']);
    }

    public function test_author_and_timestamp_cannot_be_forged(): void
    {
        $data   = $this->payload();
        $this->post('/campus/review', $data)->assertRedirect('/campus/login');
        $this->assertSame(0, DB::table('campus_reviews')->count());
        $id     = $this->student('real@example.test');
        $other  = $this->student('other@example.test');
        $this->withSession(['campus_student_id' => $id])->post('/campus/review', $data + [
            'student_id' => $other, 'username' => '偽名', 'created_at' => '1999-01-01 00:00:00',
        ])->assertSessionHasNoErrors();
        $review = DB::table('campus_reviews')->first();
        $this->assertNotNull($review);
        $this->assertSame($id, $review->student_id);
        $this->assertSame('real@example.test', $review->username);
        $this->assertNotSame('1999-01-01 00:00:00', $review->created_at);
    }

    public function test_average_uses_new_scores_only(): void
    {
        $id      = $this->student('first@example.test');
        $this->withSession(['campus_student_id' => $id])->post('/campus/review', $this->payload())->assertSessionHasNoErrors();
        $other   = $this->student('second@example.test');
        $this->withSession(['campus_student_id' => $other])->post('/campus/review', array_replace($this->payload(), ['credit_ease' => 5]))->assertSessionHasNoErrors();
        $student = DB::table('campus_students')->where('id', $id)->first();
        $this->assertNotNull($student);
        $course  = app(CourseCatalog::class)->get($student)['courses']->first();
        $this->assertNotNull($course);
        $this->assertSame(3.0, $course->averages['credit_ease']);
        $this->assertSame(2, $course->rating_counts['credit_ease']);
        DB::table('campus_reviews')->update(array_fill_keys(array_keys(CampusOptions::RATINGS), null));
        $course  = app(CourseCatalog::class)->get($student)['courses']->first();
        $this->assertNotNull($course);
        $this->assertSame(0, $course->rating_counts['credit_ease']);
    }

    public function test_post_form_lists_official_choices_and_saves_selected_course_id(): void
    {
        $id                    = $this->student('selection@example.test');
        $course                = DB::table('campus_courses')->insertGetId(['university' => '会津大学', 'academic_year' => 2026, 'course_code' => 'PL01', 'name' => '選択授業', 'professor' => '担当先生']);
        $other                 = DB::table('campus_courses')->insertGetId(['university' => 'Other', 'academic_year' => 2026, 'course_code' => 'XX01', 'name' => '他大学限定授業', 'professor' => '先生']);
        $this->withSession(['campus_student_id' => $id]);
        $this->get('/campus')->assertOk()->assertSee('course-lookup', false)->assertSee('PL01')->assertDontSee('他大学限定授業')->assertDontSee('class="course-grid"', false);
        $this->get('/campus/courses?q=PL01')->assertOk()->assertSee('検索結果')->assertSee('/campus/courses/'.$course, false);
        $this->get('/campus/courses?q=not-found')->assertOk()->assertSee('該当する授業がありません');
        $data                  = $this->payload();
        unset($data['course'], $data['professor']);
        $data['course_id']     = $course;
        $data['academic_year'] = 2026;
        $this->post('/campus/review', $data)->assertSessionHasNoErrors();
        $this->assertDatabaseHas('campus_reviews', ['course_id' => $course, 'student_id' => $id]);
        $this->assertDatabaseCount('campus_courses', 2);
        $this->post('/campus/review', array_replace($data, ['course_id' => $other]))->assertSessionHasErrors('course_id');
    }

    public function test_simplified_form_accepts_optional_fields_and_changed_year_with_twenty_character_comment(): void
    {
        $student               = $this->student('simplified@example.test');
        $course                = DB::table('campus_courses')->insertGetId(['university' => '会津大学', 'academic_year' => 2026, 'course_code' => 'SIMPLE', 'name' => '選択科目', 'professor' => '先生']);
        $data                  = $this->payload();
        unset($data['course'], $data['professor'], $data['faculty'], $data['department'], $data['materials']);
        $data['course_id']     = $course;
        $data['academic_year'] = 2024;
        $data['body']          = str_repeat('あ', 19);
        $this->withSession(['campus_student_id' => $student])->post('/campus/review', $data)->assertSessionHasErrors('body');
        $this->assertDatabaseCount('campus_reviews', 0);
        $data['body']          = str_repeat('あ', 20);
        $this->post('/campus/review', $data)->assertSessionHasNoErrors();
        $this->assertDatabaseHas('campus_reviews', ['student_id' => $student, 'course_id' => $course, 'academic_year' => 2024, 'materials' => null, 'body' => str_repeat('あ', 20)]);
        $this->get('/campus')->assertOk()->assertSee('review-step-progress', false)->assertSee('minlength="20"', false)
            ->assertSee('教材（任意）')->assertDontSee('name="faculty"', false)->assertDontSee('name="department"', false);
    }
}

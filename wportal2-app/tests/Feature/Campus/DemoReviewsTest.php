<?php

declare(strict_types=1);

namespace Tests\Feature\Campus;

use Database\Seeders\CampusDemoReviewsSeeder;
use Illuminate\Support\Facades\DB;

class DemoReviewsTest extends CampusTestCase
{
    public function test_demo_reviews_use_existing_relations_and_are_repeatable(): void
    {
        foreach (CampusDemoReviewsSeeder::COURSES as $externalId => $codes) {
            $teacher = DB::table('campus_teachers')->insertGetId(['university' => '会津大学', 'external_teacher_id' => (string) $externalId, 'source_key' => (string) $externalId, 'name' => '先生'.$externalId, 'profile_url' => '']);
            foreach ($codes as $code) {
                $course = DB::table('campus_courses')->insertGetId(['university' => '会津大学', 'academic_year' => 2026, 'course_code' => $code, 'name' => $code, 'professor' => '先生']);
                DB::table('campus_course_teachers')->insert(['teacher_id' => $teacher, 'course_id' => $course, 'role' => 'instructor', 'match_method' => 'official_id']);
            }
        }
        $this->seed(CampusDemoReviewsSeeder::class);
        $this->seed(CampusDemoReviewsSeeder::class);
        $this->assertDatabaseCount('campus_reviews', 30);
        $this->assertDatabaseCount('campus_students', 30);
        $this->assertDatabaseCount('campus_courses', 10);
        $this->assertDatabaseCount('campus_course_teachers', 10);
        $this->assertSame(30, DB::table('campus_students')->where('circle', '')->count());
        $this->assertSame(30, DB::table('campus_reviews')->where('body', 'like', '【ダミーデータ%')->count());
        foreach (DB::table('campus_reviews')->selectRaw('course_id, COUNT(*) as total')->groupBy('course_id')->get() as $group) {
            $this->assertSame(3, (int) $group->total);
        }
    }
}

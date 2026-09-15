<?php

declare(strict_types=1);

namespace Tests\Feature\Campus;

use App\Campus\CourseCatalog;
use Illuminate\Support\Facades\DB;

class ReviewMigrationTest extends CampusTestCase
{
    public function test_existing_data_is_preserved_when_migrating_to_aizu(): void
    {
        $migration = require database_path('migrations/2026_09_13_020000_update_campus_review_specification.php');
        $migration->down();
        $id        = $this->student('legacy@example.test');
        DB::table('campus_students')->where('id', $id)->update(['university' => '金沢大学']);
        $course    = DB::table('campus_courses')->insertGetId(['university' => '金沢大学', 'name' => '既存授業', 'professor' => '先生', 'created_at' => now(), 'updated_at' => now()]);
        $body      = str_repeat('旧', 600);
        $review    = DB::table('campus_reviews')->insertGetId([
            'student_id'        => $id, 'course_id' => $course, 'clarity' => 5, 'interest' => 4,
            'ease'              => 3, 'workload' => 2, 'recommendation' => 1, 'body' => $body,
            'assignment_amount' => 4, 'remote_percentage' => 75,
            'created_at'        => '2020-01-01 00:00:00', 'updated_at' => '2020-01-01 00:00:00',
        ]);
        $migration->up();
        $this->assertDatabaseHas('campus_students', ['id' => $id, 'university' => '会津大学']);
        $this->assertDatabaseHas('campus_courses', ['id' => $course, 'university' => '会津大学']);
        $this->assertDatabaseHas('campus_reviews', [
            'id'         => $review, 'body' => $body, 'clarity' => 5, 'assignment_amount' => 4, 'remote_percentage' => 75,
            'username'   => 'legacy@example.test', 'credit_ease' => null, 'assignment_load' => null,
            'created_at' => '2020-01-01 00:00:00',
        ]);
        $student   = DB::table('campus_students')->where('id', $id)->first();
        $this->assertNotNull($student);
        $data      = app(CourseCatalog::class)->get($student);
        $this->assertCount(1, $data['courses']);
        $item      = $data['courses']->first();
        $this->assertNotNull($item);
        $this->assertSame(0, $item->rating_counts['credit_ease']);
        $this->withSession(['campus_student_id' => $id])->get('/campus')->assertOk();
    }
}

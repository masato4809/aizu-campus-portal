<?php

declare(strict_types=1);

namespace Tests\Feature\Campus;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

abstract class CampusTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'session.driver' => 'array', 'cache.default' => 'array']);
        DB::purge('sqlite');
        $migration      = require database_path('migrations/2026_09_13_000000_create_campus_tables.php');
        $migration->up();

        $pastExams      = require database_path('migrations/2026_09_13_193144_add_past_exam_count_to_campus_students_table.php');
        $pastExams->up();
        $details        = require database_path('migrations/2026_09_13_010000_add_details_to_campus_reviews.php');
        $details->up();
        $spec           = require database_path('migrations/2026_09_13_020000_update_campus_review_specification.php');
        $spec->up();

        $contact        = require database_path('migrations/2026_09_13_020000_add_contact_url_to_campus_clubs.php');
        $contact->up();

        $website        = require database_path('migrations/2026_09_13_030000_add_website_url_to_campus_clubs.php');
        $website->up();

        $diagnosis      = require database_path('migrations/2026_09_13_040000_add_diagnosis_fields_to_campus_students.php');
        $diagnosis->up();

        $clubCatalog    = require database_path('migrations/2026_09_14_020000_create_campus_club_catalog.php');
        $clubCatalog->up();

        $catalogImages  = require database_path('migrations/2026_09_14_030000_add_images_to_campus_club_catalog.php');
        $catalogImages->up();

        $catalog        = require database_path('migrations/2026_09_14_000000_add_official_campus_catalog.php');
        $catalog->up();

        $teacherReviews = require database_path('migrations/2026_09_14_010000_create_campus_teacher_reviews.php');
        $teacherReviews->up();

        $helpful        = require database_path('migrations/2026_09_14_020000_create_campus_review_helpful_votes.php');
        $helpful->up();
        $weights        = require database_path('migrations/2026_09_14_030000_update_campus_review_evaluation_fields.php');
        $weights->up();

        $clubImages     = require database_path('migrations/2026_09_14_010000_add_campus_club_images_and_ranking_index.php');
        $clubImages->up();

        $removeLearning = require database_path('migrations/2026_09_14_050000_remove_learning_style_from_campus_students.php');
        $removeLearning->up();
    }

    protected function student(string $email, int $year = 1): int
    {
        return DB::table('campus_students')->insertGetId(['name' => $email, 'email' => $email, 'password' => Hash::make('password1234'), 'university' => '会津大学', 'faculty' => '理工学域', 'year' => $year, 'circle' => 'CUO', 'personality' => 'AAA', 'goal_orientation' => 'high_grade', 'created_at' => now(), 'updated_at' => now()]);
    }
}

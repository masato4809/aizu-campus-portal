<?php

declare(strict_types=1);

namespace Tests\Feature\Campus;

use App\Campus\Export\AizuCsvExporter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AizuExportTest extends CampusTestCase
{
    public function test_export_preserves_values_filters_year_and_reports_missing_data(): void
    {
        $directory = storage_path('framework/testing/aizu-export-'.bin2hex(random_bytes(6)));
        try {
            $name     = "日本語,授業\n\"引用\"";
            $course   = DB::table('campus_courses')->insertGetId(['university' => '会津大学', 'academic_year' => 2026, 'course_code' => 'PL01', 'name' => $name, 'professor' => '', 'offerings' => json_encode([['credits' => '2', 'teachers' => [['name' => 'Unknown Person', 'role' => 'instructor']]]])]);
            DB::table('campus_courses')->insert(['university' => '会津大学', 'academic_year' => 2025, 'course_code' => 'OLD', 'name' => 'Old', 'professor' => '']);
            DB::table('campus_courses')->insert(['university' => 'Other', 'academic_year' => 2026, 'course_code' => 'OTHER', 'name' => 'Other', 'professor' => '']);
            DB::table('campus_courses')->insert(['university' => '会津大学', 'academic_year' => 2026, 'course_code' => 'PL02', 'name' => 'No teacher', 'professor' => '']);
            $teacher  = DB::table('campus_teachers')->insertGetId(['university' => '会津大学', 'source_key' => 'one', 'name' => '教員', 'email' => 'public@example.test', 'profile_url' => 'https://u-aizu.ac.jp/example']);
            $second   = DB::table('campus_teachers')->insertGetId(['university' => '会津大学', 'source_key' => 'two', 'name' => '別教員', 'profile_url' => '']);
            foreach ([$teacher, $second] as $id) {
                DB::table('campus_course_teachers')->insert(['course_id' => $course, 'teacher_id' => $id, 'role' => 'instructor', 'match_method' => 'official_id']);
            }
            $exporter = new AizuCsvExporter;
            $result   = $exporter->export(2026, $directory);
            $this->assertSame(2, $result['courses']);
            $this->assertSame(2, $result['teachers']);
            $this->assertSame(2, $result['relations']);
            $this->assertSame(3, $result['catalog']);
            $rows     = $this->readCsv($directory.'/courses.csv');
            $this->assertSame($name, $rows[1][2]);
            $this->assertSame('', $rows[1][3]);
            $this->assertSame('2', $rows[1][9]);
            $issues   = array_column(array_slice($this->readCsv($directory.'/aizu_import_issues_2026.csv'), 1), 0);
            foreach (['unmatched_teacher', 'no_teachers', 'missing_email', 'missing_profile_url', 'missing_syllabus_url'] as $issue) {
                $this->assertContains($issue, $issues);
            }
            $this->assertContains('public@example.test', array_column($this->readCsv($directory.'/course_teachers.csv'), 6));
            $original = file_get_contents($directory.'/aizu_catalog_2026.csv');
            $this->assertSame($result, $exporter->export(2026, $directory));
            $this->assertSame($original, file_get_contents($directory.'/aizu_catalog_2026.csv'));
            $this->assertSame(4, DB::table('campus_courses')->count());
            $empty    = $exporter->export(2024, $directory.'/empty');
            $this->assertSame(0, $empty['courses']);
            $this->assertCount(1, $this->readCsv($directory.'/empty/courses.csv'));
        } finally {
            File::deleteDirectory($directory);
        }
    }

    /** @return list<list<string|null>> */
    private function readCsv(string $path): array
    {
        $handle = fopen($path, 'rb');
        $this->assertNotFalse($handle);
        $this->assertSame("\xEF\xBB\xBF", fread($handle, 3));
        $rows   = [];
        while (($row = fgetcsv($handle, 0, ',', '"', '')) !== false) {
            $rows[] = $row;
        }
        fclose($handle);

        return $rows;
    }
}

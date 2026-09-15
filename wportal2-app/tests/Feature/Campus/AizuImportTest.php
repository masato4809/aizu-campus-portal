<?php

declare(strict_types=1);

namespace Tests\Feature\Campus;

use App\Campus\CourseCatalog;
use App\Campus\CourseIdentity;
use App\Campus\Import\AizuImporter;
use App\Campus\Import\ImportReport;
use App\Campus\Import\NameMatcher;
use App\Campus\Import\OfficialHttp;
use App\Campus\Import\SyllabusParser;
use App\Campus\Import\TeacherParser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AizuImportTest extends CampusTestCase
{
    private function fixture(string $name): string
    {
        return (string) file_get_contents(base_path('tests/Fixtures/UAizu/'.$name.'.html'));
    }

    private function fakeSources(bool $broken = false): void
    {
        Http::preventStrayRequests();
        $base = AizuImporter::BASE;
        Http::fake([
            $base.'2026_1_J_000.html'                               => Http::response($this->fixture('list-ja')),
            $base.'2026_1_E_000.html'                               => Http::response($this->fixture('list-en')),
            $base.'2026_2_J_000.html'                               => Http::response($this->fixture('list-ja')),
            $base.'2026_2_E_000.html'                               => Http::response($this->fixture('list-en')),
            $base.'2026_1_J_012.html'                               => $broken ? Http::response('missing', 404) : Http::response($this->fixture('courses-ja')),
            $base.'2026_1_E_012.html'                               => Http::response($this->fixture('courses-en')),
            AizuImporter::FACULTY                                   => Http::response($this->fixture('faculty')),
            'https://u-aizu.ac.jp/research/faculty/detail?cd=90137' => Http::response($this->fixture('teacher')),
            'https://u-aizu.ac.jp/research/faculty/detail?cd=99999' => Http::response('missing profile', 404),
        ]);
    }

    public function test_course_parser_reads_code_title_multiple_teachers_and_ignores_other_sections(): void
    {
        $parser    = new SyllabusParser;
        $entries   = $parser->listing($this->fixture('list-ja'), AizuImporter::BASE.'2026_1_J_000.html', 2026);
        $this->assertCount(2, $entries);
        $data      = $parser->course($this->fixture('courses-ja'), $entries[0], 2026);
        $this->assertSame('PL01', $data['course_code']);
        $this->assertSame('プログラミング入門', $data['name']);
        $this->assertCount(4, $data['teachers']);
        $this->assertSame('coordinator', $data['teachers'][0]['role']);
        $this->assertSame('90137', $data['teachers'][1]['external_teacher_id']);
        $this->assertStringContainsString('Second Quarter', $data['semester']);
        $malformed = str_replace('</table></div></div>', '</table>', $this->fixture('courses-ja'));
        $isolated  = $parser->course($malformed, $entries[0], 2026);
        $this->assertCount(4, $isolated['teachers']);
        $this->assertStringContainsString('Second Quarter', $isolated['semester']);
        $this->expectException(RuntimeException::class);
        $parser->listing(str_replace('2026', '2025', $this->fixture('list-ja')), AizuImporter::BASE, 2026);
    }

    public function test_teacher_profile_email_and_bilingual_name_are_scoped_to_profile(): void
    {
        $parser  = new TeacherParser;
        $profile = $parser->profile($this->fixture('teacher'), 'https://u-aizu.ac.jp/research/faculty/detail?cd=90137');
        $this->assertSame('90137', $profile['external_teacher_id']);
        $this->assertSame('新田 高庸', $profile['name']);
        $this->assertSame('NITTA Koyo', $profile['name_en']);
        $this->assertSame('koyo@u-aizu.ac.jp', $profile['email']);
        $this->assertNull($profile['laboratory_name']);
        $without = str_replace('koyo@u-aizu.ac.jp', '', $this->fixture('teacher'));
        $this->assertNull($parser->profile($without, $profile['profile_url'])['email']);
    }

    public function test_name_matching_handles_order_punctuation_japanese_and_ambiguity(): void
    {
        $matcher    = new NameMatcher;
        $teachers   = [['id' => 1, 'external_teacher_id' => '90137', 'name' => '新田 高庸', 'name_en' => 'NITTA Koyo', 'aliases' => ['新田高庸']]];
        foreach (['NITTA Koyo', 'Koyo NITTA', 'nitta.  koyo', 'Ｋｏｙｏ　ＮＩＴＴＡ', '新田　高庸'] as $name) {
            $this->assertSame(1, $matcher->match($name, null, $teachers)['teacher_id']);
        }
        $this->assertSame(1, $matcher->match('表記変更', '90137', $teachers)['teacher_id']);
        $this->assertNull($matcher->match('NITTA Koyo', '999', $teachers)['teacher_id']);
        $this->assertNull($matcher->match('NITTA K.', null, $teachers)['teacher_id']);
        $compound   = [['id' => 3, 'external_teacher_id' => '90300', 'name' => '複合姓名', 'name_en' => 'BEN ABDALLAH Abderazek', 'aliases' => []]];
        $this->assertSame(3, $matcher->match('Abderazek BEN ABDALLAH', null, $compound)['teacher_id']);
        $teachers[] = array_replace($teachers[0], ['id' => 2, 'external_teacher_id' => '90200']);
        $this->assertNull($matcher->match('NITTA Koyo', null, $teachers)['teacher_id']);
    }

    public function test_import_is_idempotent_reports_failures_and_preserves_reviews(): void
    {
        $this->fakeSources();
        $studentId = $this->student('reviewer@example.test');
        $courseId  = DB::table('campus_courses')->insertGetId(['university' => '会津大学', 'name' => 'プログラミング入門', 'professor' => '新田 高庸', 'created_at' => now(), 'updated_at' => now()]);
        $reviewId  = DB::table('campus_reviews')->insertGetId(['student_id' => $studentId, 'course_id' => $courseId, 'body' => '既存口コミ', 'created_at' => now(), 'updated_at' => now()]);
        $importer  = new AizuImporter;
        $report    = new ImportReport;
        $importer->run(new OfficialHttp(0, true), $report);
        $this->assertSame(2, DB::table('campus_courses')->count());
        $this->assertSame(1, DB::table('campus_teachers')->count());
        $this->assertSame(3, DB::table('campus_course_teachers')->count());
        $this->assertDatabaseHas('campus_courses', ['id' => $courseId, 'course_code' => 'PL01', 'academic_year' => 2026, 'name_en' => 'Intro.Programming']);
        $this->assertDatabaseHas('campus_reviews', ['id' => $reviewId, 'course_id' => $courseId, 'body' => '既存口コミ']);
        $this->assertGreaterThan(0, $report->counts['Errors']);
        $this->assertGreaterThan(0, $report->counts['Teachers unmatched']);
        $this->assertStringContainsString('unmatched_teacher', (string) file_get_contents($report->path));
        $second    = new ImportReport;
        $importer->run(new OfficialHttp(0, true), $second);
        $this->assertSame(0, $second->counts['Courses inserted']);
        $this->assertSame(2, $second->counts['Courses updated']);
        $this->assertSame(1, $second->counts['Teachers updated']);
        $this->assertSame(3, DB::table('campus_course_teachers')->count());
        $student   = DB::table('campus_students')->where('id', $studentId)->first();
        $this->assertNotNull($student);
        $this->assertCount(2, app(CourseCatalog::class)->get($student, 'NITTA')['courses']);
        $this->assertSame($courseId, app(CourseIdentity::class)->resolve($student, ['course' => 'プログラミング入門', 'professor' => '新田 高庸', 'academic_year' => 2026]));
    }

    public function test_dry_run_and_non_2026_do_not_write_and_partial_retry_preserves_relations(): void
    {
        $this->fakeSources();
        $importer = new AizuImporter;
        $importer->run(new OfficialHttp(0, true), new ImportReport, true);
        $this->assertSame(0, DB::table('campus_courses')->count());
        $this->assertSame(0, DB::table('campus_teachers')->count());
        $command  = $this->artisan('campus:import-u-aizu', ['--year' => 2025]);
        $this->assertInstanceOf(\Illuminate\Testing\PendingCommand::class, $command);
        $command->assertFailed();
        $importer->run(new OfficialHttp(0, true), new ImportReport);
        $this->fakeSources(true);
        $report   = new ImportReport;
        $importer->run(new OfficialHttp(0, true), $report);
        $this->assertSame(2, DB::table('campus_courses')->count());
        $this->assertSame(3, DB::table('campus_course_teachers')->count());
        $this->assertGreaterThan(0, $report->counts['Errors']);
    }

    public function test_multiple_sections_share_a_code_without_losing_details(): void
    {
        $store   = new \App\Campus\Import\ImportStore;
        $report  = new ImportReport;
        $parser  = new SyllabusParser;
        $entry   = $parser->listing($this->fixture('list-ja'), AizuImporter::BASE.'2026_1_J_000.html', 2026)[0];
        $first   = $parser->course($this->fixture('courses-ja'), $entry, 2026) + ['language' => 'J'];
        $second  = array_replace($first, ['section_id' => 'SS-other', 'name' => 'プログラミング入門 別クラス', 'syllabus_url' => AizuImporter::BASE.'2026_1_J_012.html#SS-other']);
        $store->course('PL01', [$first, $second], [], $report, true);
        $course  = DB::table('campus_courses')->first();
        $this->assertNotNull($course);
        $this->assertCount(2, json_decode($course->offerings, true));
        $store->course('PL01', [$first, $second], [], $report, true);
        $this->assertSame(1, DB::table('campus_courses')->count());
        $other   = $this->student('other-university@example.test');
        DB::table('campus_students')->where('id', $other)->update(['university' => '別大学']);
        $student = DB::table('campus_students')->where('id', $other)->first();
        $this->assertNotNull($student);
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(CourseIdentity::class)->resolve($student, ['course_id' => $course->id, 'academic_year' => 2026]);
    }

    public function test_failed_http_page_is_not_retried_for_each_section(): void
    {
        Http::preventStrayRequests();
        Http::fake(['https://u-aizu.ac.jp/missing' => Http::response('', 404)]);
        $http = new OfficialHttp(0, true);
        foreach (['#one', '#two'] as $fragment) {
            try {
                $http->get('https://u-aizu.ac.jp/missing'.$fragment);
                $this->fail('Must fail');
            } catch (RuntimeException $error) {
                $this->assertStringContainsString('404', $error->getMessage());
            }
        }
        Http::assertSentCount(1);
    }

    public function test_official_http_does_not_follow_external_redirect(): void
    {
        Http::preventStrayRequests();
        Http::fake(['https://u-aizu.ac.jp/test' => Http::response('', 302, ['Location' => 'https://example.com/private'])]);
        try {
            (new OfficialHttp(0, true))->get('https://u-aizu.ac.jp/test');
            $this->fail('External redirect must be rejected');
        } catch (RuntimeException $error) {
            $this->assertStringContainsString('Non-official', $error->getMessage());
        }
        Http::assertSentCount(1);
    }
}

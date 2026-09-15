<?php

declare(strict_types=1);

namespace App\Campus\Export;

use App\Campus\CampusOptions;
use App\Campus\Import\NameMatcher;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AizuCsvExporter
{
    /** @return array{courses: int, teachers: int, relations: int, catalog: int, issues: int, files: list<string>} */
    public function export(int $year, string $directory): array
    {
        // Read all three tables in one transaction before writing any files.
        [$courses, $teachers, $relations] = DB::transaction(function () use ($year) {
            $courses   = DB::table('campus_courses')->where('university', CampusOptions::UNIVERSITY)
                ->where('academic_year', $year)->orderBy('course_code')->orderBy('id')->get();
            $teachers  = DB::table('campus_teachers')->where('university', CampusOptions::UNIVERSITY)->orderBy('name')->orderBy('id')->get();
            $relations = DB::table('campus_course_teachers')->whereIn('course_id', $courses->pluck('id'))->orderBy('teacher_id')->orderBy('role')->get();

            return [$courses, $teachers, $relations];
        });
        $teacherMap                       = $teachers->keyBy('id');
        $byCourse                         = $relations->groupBy('course_id');
        $matcherTeachers                  = $teachers->map(function ($teacher) {
            $row            = (array) $teacher;
            $row['aliases'] = json_decode($teacher->aliases ?? '[]', true, 512, JSON_THROW_ON_ERROR);

            return $row;
        })->values()->all();
        $issues                           = [];
        $addIssue                         = function (string $type, string $code, string $course, string $teacher, string $details) use (&$issues): void {
            $row                                            = [$type, $code, $course, $teacher, $details];
            $issues[json_encode($row, JSON_THROW_ON_ERROR)] = $row;
        };
        $courseRows                       = $relationRows = $catalogRows = [];
        foreach ($courses as $course) {
            $offerings    = json_decode($course->offerings ?? '[]', true, 512, JSON_THROW_ON_ERROR);
            $extras       = [];
            foreach (['day', 'period', 'credits', 'teaching_mode', 'classroom'] as $key) {
                $values   = array_values(array_unique(array_filter(array_column($offerings, $key), fn ($v) => $v !== null && $v !== '')));
                $extras[] = implode(' / ', $values);
            }
            $courseRows[] = array_merge([$course->id, $course->course_code, $course->name, $course->name_en, $course->academic_year, $course->semester, $course->syllabus_url], $extras, [$course->description, $course->offerings]);
            $links        = $byCourse->get($course->id, collect())->sortBy(fn ($r) => [$teacherMap->get($r->teacher_id)->name ?? '', $r->teacher_id, $r->role]);
            if ($links->isEmpty()) {
                $addIssue('no_teachers', $course->course_code ?? '', $course->name, '', 'No saved course-teacher relations');
                $catalogRows[] = [$course->course_code, $course->name, $year, $course->semester, null, null, null, null, $course->syllabus_url, null];
            }
            if (! $course->syllabus_url) {
                $addIssue('missing_syllabus_url', $course->course_code ?? '', $course->name, '', 'syllabus_url is empty');
            }
            foreach ($links as $link) {
                $teacher        = $teacherMap->get($link->teacher_id);
                $relationRows[] = [$course->id, $course->course_code, $course->name, $link->teacher_id, $teacher?->name, $teacher?->name_en, $teacher?->email, $link->role];
                $catalogRows[]  = [$course->course_code, $course->name, $year, $course->semester, $teacher?->name, $teacher?->name_en, $teacher?->email, $link->role, $course->syllabus_url, $teacher?->profile_url];
                if (! $teacher) {
                    $addIssue('invalid_teacher_relation', $course->course_code ?? '', $course->name, '', 'teacher_id='.$link->teacher_id.' is absent from university teachers');
                }
            }
            foreach ($offerings as $offering) {
                foreach ($offering['teachers'] ?? [] as $mention) {
                    $match = (new NameMatcher)->match($mention['name'], $mention['external_teacher_id'] ?? null, array_values($matcherTeachers));
                    if ($match['teacher_id'] === null) {
                        $addIssue('unmatched_teacher', $course->course_code ?? '', $course->name, $mention['name'], 'Current DB matching: '.$match['method'].'; role='.$mention['role']);
                    } elseif (! $links->contains(fn ($r) => (int) $r->teacher_id === $match['teacher_id'] && $r->role === $mention['role'])) {
                        $addIssue('missing_teacher_relation', $course->course_code ?? '', $course->name, $mention['name'], 'Matched teacher_id='.$match['teacher_id'].' but role='.$mention['role'].' relation is absent');
                    }
                }
            }
        }
        $teacherRows                      = [];
        foreach ($teachers as $teacher) {
            $teacherRows[] = [$teacher->id, $teacher->external_teacher_id, $teacher->name, $teacher->name_en, $teacher->email, $teacher->department, $teacher->position, $teacher->research_field, $teacher->laboratory_name, $teacher->profile_url, $teacher->laboratory_url, $teacher->personal_url];
            foreach (['email', 'profile_url'] as $field) {
                if (! $teacher->{$field}) {
                    $addIssue('missing_'.$field, '', '', $teacher->name, 'teacher_id='.$teacher->id.'; '.$field.' is empty');
                }
            }
        }
        foreach ($courses->groupBy('course_code') as $code => $group) {
            if ($group->count() > 1) {
                $addIssue('duplicate_course_code', (string) $code, '', '', 'course_ids='.$group->pluck('id')->implode('|'));
            }
        }
        foreach ($teachers as $index => $teacher) {
            foreach ($teachers->slice($index + 1) as $other) {
                $a            = (array) $other;
                $a['aliases'] = json_decode($other->aliases ?? '[]', true, 512, JSON_THROW_ON_ERROR);
                $match        = (new NameMatcher)->match($teacher->name_en ?: $teacher->name, null, [$a]);
                if ($match['teacher_id'] !== null || ($teacher->email && $teacher->email === $other->email)) {
                    $addIssue('possible_duplicate_teacher', '', '', $teacher->name, 'Review only; teacher_ids='.$teacher->id.'|'.$other->id.'; other_name='.$other->name);
                }
            }
        }
        $issueRows                        = array_values($issues);
        usort($issueRows, fn ($a, $b) => $a <=> $b);
        $files                            = [];
        $files[]                          = $this->write($directory, 'courses.csv', ['course_id', 'course_code', 'course_name', 'course_name_en', 'academic_year', 'semester', 'syllabus_url', 'day', 'period', 'credits', 'teaching_mode', 'classroom', 'description', 'offerings_json'], $courseRows);
        $files[]                          = $this->write($directory, 'teachers.csv', ['teacher_id', 'external_teacher_id', 'name', 'name_en', 'email', 'department', 'position', 'research_field', 'laboratory_name', 'profile_url', 'laboratory_url', 'personal_url'], $teacherRows);
        $files[]                          = $this->write($directory, 'course_teachers.csv', ['course_id', 'course_code', 'course_name', 'teacher_id', 'teacher_name', 'teacher_name_en', 'email', 'role'], $relationRows);
        $files[]                          = $this->write($directory, 'aizu_catalog_'.$year.'.csv', ['course_code', 'course_name', 'academic_year', 'semester', 'teacher_name', 'teacher_name_en', 'email', 'role', 'syllabus_url', 'profile_url'], $catalogRows);
        $files[]                          = $this->write($directory, 'aizu_import_issues_'.$year.'.csv', ['issue_type', 'course_code', 'course_name', 'teacher_name', 'details'], $issueRows);

        return ['courses' => count($courseRows), 'teachers' => count($teacherRows), 'relations' => count($relationRows), 'catalog' => count($catalogRows), 'issues' => count($issueRows), 'files' => $files];
    }

    /** @param list<string> $headers
     * @param  list<list<mixed>>  $rows
     */
    private function write(string $directory, string $name, array $headers, array $rows): string
    {
        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException('Cannot create export directory: '.$directory);
        }
        $temporary = tempnam($directory, '.csv-');
        if ($temporary === false) {
            throw new RuntimeException('Cannot create temporary CSV');
        }
        $handle    = fopen($temporary, 'wb');
        if ($handle === false) {
            unlink($temporary);
            throw new RuntimeException('Cannot open temporary CSV');
        }
        try {
            if (fwrite($handle, "\xEF\xBB\xBF") !== 3) {
                throw new RuntimeException('Cannot write CSV BOM');
            }
            foreach (array_merge([$headers], $rows) as $row) {
                if (fputcsv($handle, array_map(fn ($v) => $v === null ? '' : (string) $v, $row), ',', '"', '', "\r\n") === false) {
                    throw new RuntimeException('Cannot write CSV row');
                }
            }
            if (! fflush($handle)) {
                throw new RuntimeException('Cannot flush CSV');
            }
            fclose($handle);
            $handle = null;
            if (! rename($temporary, $directory.'/'.$name)) {
                throw new RuntimeException('Cannot replace CSV: '.$name);
            }
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
            if (is_file($temporary)) {
                unlink($temporary);
            }
        }

        return $directory.'/'.$name;
    }
}

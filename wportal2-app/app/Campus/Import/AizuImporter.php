<?php

declare(strict_types=1);

namespace App\Campus\Import;

use App\Campus\CampusOptions;
use Closure;
use Illuminate\Support\Facades\DB;
use Throwable;

class AizuImporter
{
    public const BASE    = 'https://web-ext.u-aizu.ac.jp/official/curriculum/syllabus/';

    public const FACULTY = 'https://u-aizu.ac.jp/intro/faculty/cse/';

    public function run(OfficialHttp $http, ImportReport $report, bool $dryRun = false, int $limit = 0, ?Closure $progress = null): void
    {
        $parser                           = new SyllabusParser;
        $entries                          = [];
        $indexesComplete                  = true;
        foreach ([1, 2] as $level) {
            foreach (['J', 'E'] as $language) {
                $url = self::BASE.'2026_'.$level.'_'.$language.'_000.html';
                try {
                    foreach ($parser->listing($http->get($url), $url, 2026) as $entry) {
                        $entries[$entry['course_code']][$entry['url']] = $entry + ['language' => $language];
                    }
                } catch (Throwable $error) {
                    $indexesComplete = false;
                    $report->event('error', ['url' => $url, 'message' => $error->getMessage()]);
                }
            }
        }
        $report->counts['Courses found']  = count($entries);
        ksort($entries);
        if ($limit > 0) {
            $entries = array_slice($entries, 0, $limit, true);
        }
        $courses                          = [];
        $complete                         = [];
        foreach ($entries as $code => $sections) {
            $complete[$code] = $indexesComplete;
            foreach ($sections as $entry) {
                try {
                    $courses[$code][] = $parser->course($http->get($entry['url']), $entry, 2026) + ['language' => $entry['language']];
                } catch (Throwable $error) {
                    $complete[$code] = false;
                    $report->event('error', ['url' => $entry['url'], 'course_code' => $code, 'message' => $error->getMessage()]);
                }
            }
            $progress?->__invoke('Parsed course '.$code);
        }
        $teacherParser                    = new TeacherParser;
        $teacherEntries                   = [];
        try {
            $teacherEntries = $teacherParser->listing($http->get(self::FACULTY), self::FACULTY);
        } catch (Throwable $error) {
            $report->event('error', ['url' => self::FACULTY, 'message' => $error->getMessage()]);
        }
        $report->counts['Teachers found'] = count($teacherEntries);
        $store                            = new ImportStore;
        $parsedTeachers                   = [];
        foreach ($teacherEntries as $entry) {
            try {
                $teacher            = $teacherParser->profile($http->get($entry['url']), $entry['url']);
                $teacher['aliases'] = array_values(array_unique([$teacher['name'], $entry['alias']]));
                foreach (['email', 'laboratory_name', 'laboratory_url'] as $field) {
                    if ($teacher[$field] === null) {
                        $report->event('missing_field', ['url' => $entry['url'], 'field' => $field]);
                    }
                }
                $teacher['id']      = $dryRun ? count($parsedTeachers) + 1 : $store->teacher($teacher, $report);
                $parsedTeachers[]   = $teacher;
            } catch (Throwable $error) {
                $report->event('error', ['url' => $entry['url'], 'message' => $error->getMessage()]);
            }
            $progress?->__invoke('Parsed teacher profile '.$entry['url']);
        }
        $teachers                         = $dryRun ? $parsedTeachers : DB::table('campus_teachers')->where('university', CampusOptions::UNIVERSITY)->get()->map(function ($row) {
            $data            = (array) $row;
            $data['aliases'] = json_decode($data['aliases'] ?? '[]', true);

            return $data;
        })->all();
        $teachers                         = array_values($teachers);
        foreach ($courses as $code => $offerings) {
            $before = $report->counts;
            try {
                if ($dryRun) {
                    foreach ($offerings as $offering) {
                        foreach ($offering['teachers'] as $mention) {
                            $match = (new NameMatcher)->match($mention['name'], $mention['external_teacher_id'], $teachers);
                            $report->counts[$match['teacher_id'] === null ? 'Teachers unmatched' : 'Teachers matched']++;
                            if ($match['teacher_id'] === null) {
                                $report->event('unmatched_teacher', ['course_code' => $code, 'name' => $mention['name'], 'reason' => $match['method']]);
                            }
                        }
                    }
                } else {
                    $store->course($code, $offerings, $teachers, $report, $complete[$code]);
                }
            } catch (Throwable $error) {
                $report->counts = $before;
                $report->event('error', ['course_code' => $code, 'message' => $error->getMessage()]);
            }
        }
        $report->event('summary', ['dry_run' => $dryRun, 'limit' => $limit, 'counts' => $report->counts]);
    }
}

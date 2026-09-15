<?php

declare(strict_types=1);

namespace App\Campus\Import;

use App\Campus\CampusOptions;
use Illuminate\Support\Facades\DB;

class ImportStore
{
    /** @param array<string, mixed> $data */
    public function teacher(array $data, ImportReport $report): int
    {
        $identity                  = ['university' => CampusOptions::UNIVERSITY, 'source_key' => hash('sha256', $data['external_teacher_id'] !== null ? 'id:'.$data['external_teacher_id'] : $data['profile_url'])];
        $existing                  = DB::table('campus_teachers')->where($identity)->first();
        $data['aliases']           = json_encode(array_values(array_unique(array_merge($data['aliases'], $existing ? json_decode($existing->aliases ?? '[]', true) : []))), JSON_THROW_ON_ERROR);
        $data['source_fetched_at'] = now();
        $data['updated_at']        = now();
        if ($existing) {
            // An absent field must not erase previously obtained official information.
            DB::table('campus_teachers')->where('id', $existing->id)->update(array_filter($data, fn ($value) => $value !== null));
            $report->counts['Teachers updated']++;

            return $existing->id;
        }
        $id                        = DB::table('campus_teachers')->insertGetId($identity + $data + ['created_at' => now()]);
        $report->counts['Teachers inserted']++;

        return $id;
    }

    /** @param list<array<string, mixed>> $offerings
     * @param  list<array<string, mixed>>  $teachers
     */
    public function course(string $code, array $offerings, array $teachers, ImportReport $report, bool $complete): void
    {
        DB::transaction(function () use ($code, $offerings, $teachers, $report, $complete) {
            $identity  = ['university' => CampusOptions::UNIVERSITY, 'academic_year' => 2026, 'course_code' => $code];
            $existing  = DB::table('campus_courses')->where($identity)->first();
            if ($existing && ! $complete) {
                $old       = json_decode($existing->offerings ?? '[]', true);
                $byUrl     = [];
                foreach (array_merge($old, $offerings) as $offering) {
                    $byUrl[$offering['syllabus_url']] = $offering;
                }
                $offerings = array_values($byUrl);
            }
            usort($offerings, fn ($a, $b) => [$a['language'] !== 'J', $a['section_id']] <=> [$b['language'] !== 'J', $b['section_id']]);
            $primary   = $offerings[0];
            $english   = array_values(array_filter($offerings, fn ($o) => $o['language'] === 'E'));
            $names     = [];
            $mentions  = [];
            foreach ($offerings as $offering) {
                foreach ($offering['teachers'] as $mention) {
                    $mentions[$mention['role'].'|'.$mention['name'].'|'.($mention['external_teacher_id'] ?? '')] = $mention;
                    if ($offering['language'] === $primary['language'] && $mention['role'] === 'instructor') {
                        $names[] = $mention['name'];
                    }
                }
            }
            $professor = implode('、', array_unique($names));
            if (! $existing) {
                $legacy     = DB::table('campus_courses')->where('university', CampusOptions::UNIVERSITY)->whereNull('course_code')->where('name', $primary['name'])
                    ->whereNotExists(function ($reviews) {
                        $reviews->selectRaw('1')->from('campus_reviews')->whereColumn('campus_reviews.course_id', 'campus_courses.id')->where('campus_reviews.academic_year', '!=', 2026);
                    })->get();
                $candidates = $legacy->filter(fn ($row) => in_array(NameMatcher::compact($row->professor), array_map(NameMatcher::compact(...), array_merge($names, [$professor])), true));
                if ($candidates->count() === 1) {
                    $existing = $candidates->first(); // Preserve the ID referenced by existing reviews.
                } elseif ($legacy->isNotEmpty()) {
                    $report->event('legacy_not_merged', ['course_code' => $code, 'candidate_ids' => $legacy->pluck('id')->all()]);
                }
            }
            $data      = $identity + [
                'legacy_key'        => null, 'name' => $primary['name'], 'name_en' => $english[0]['name'] ?? null, 'professor' => $professor,
                'semester'          => implode(' / ', array_unique(array_column($offerings, 'semester'))),
                'syllabus_url'      => $primary['syllabus_url'], 'description' => $primary['description'] ?? null,
                'offerings'         => json_encode($offerings, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'source_fetched_at' => now(), 'updated_at' => now(),
            ];
            if ($existing) {
                DB::table('campus_courses')->where('id', $existing->id)->update($data);
                $id = $existing->id;
                $report->counts['Courses updated']++;
            } else {
                $id = DB::table('campus_courses')->insertGetId($data + ['created_at' => now()]);
                $report->counts['Courses inserted']++;
            }
            $relations = [];
            $unmatched = false;
            foreach ($mentions as $mention) {
                $match                                                = (new NameMatcher)->match($mention['name'], $mention['external_teacher_id'], $teachers);
                if ($match['teacher_id'] === null) {
                    $unmatched = true;
                    $report->counts['Teachers unmatched']++;
                    $report->event('unmatched_teacher', ['course_code' => $code, 'name' => $mention['name'], 'role' => $mention['role'], 'reason' => $match['method'], 'syllabus_url' => $primary['syllabus_url']]);

                    continue;
                }
                $report->counts['Teachers matched']++;
                $relations[$match['teacher_id'].'|'.$mention['role']] = ['course_id' => $id, 'teacher_id' => $match['teacher_id'], 'role' => $mention['role'], 'match_method' => $match['method'], 'created_at' => now(), 'updated_at' => now()];
            }
            // Only replace old relationships if every source/name was successfully processed.
            if ($complete && ! $unmatched && $mentions !== []) {
                $stale = DB::table('campus_course_teachers')->where('course_id', $id)->get()
                    ->filter(fn ($row) => ! isset($relations[$row->teacher_id.'|'.$row->role]))->pluck('id');
                DB::table('campus_course_teachers')->whereIn('id', $stale)->delete();
            }
            if ($relations) {
                DB::table('campus_course_teachers')->upsert(array_values($relations), ['course_id', 'teacher_id', 'role'], ['match_method', 'updated_at']);
            }
            $report->counts['Course-teacher relations'] += DB::table('campus_course_teachers')->where('course_id', $id)->count();
        });
    }
}

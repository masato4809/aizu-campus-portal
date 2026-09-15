<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Campus\CampusOptions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/** Adds deterministic demo profiles until 1,000 demo students exist. */
class CampusThousandDemoStudentsSeeder extends Seeder
{
    public function run(): void
    {
        $prefix   = 'campus-demo-1000-';
        $existing = DB::table('campus_students')->where('university', CampusOptions::UNIVERSITY)
            ->where('email', 'like', 'campus-demo-%@example.invalid')->count();
        $clubs    = DB::table('campus_club_catalog')->where('university', CampusOptions::UNIVERSITY)->pluck('name')->values();
        $courses  = DB::table('campus_courses')->where('university', CampusOptions::UNIVERSITY)->pluck('id')->values();
        $types    = array_keys(CampusOptions::USER_TYPES);

        DB::transaction(function () use ($existing, $prefix, $clubs, $courses, $types): void {
            for ($number = $existing + 1; $number <= 1000; $number++) {
                $club = $clubs->isNotEmpty() ? $clubs[($number - 1) % $clubs->count()] : '';
                DB::table('campus_students')->insert([
                    'name'             => sprintf('会津デモ学生%04d', $number),
                    'email'            => sprintf('%s%04d@example.invalid', $prefix, $number),
                    'password'         => Hash::make('demo-password-2026'),
                    'university'       => CampusOptions::UNIVERSITY,
                    'faculty'          => 'コンピュータ理工学部',
                    'year'             => (($number - 1) % 4) + 1,
                    'circle'           => $club,
                    'personality'      => CampusOptions::TYPES[($number - 1) % count(CampusOptions::TYPES)],
                    'learning_style'   => ['practical', 'theoretical', 'balanced'][($number - 1) % 3],
                    'goal_orientation' => ['high_grade', 'min_effort', 'balanced'][($number - 1) % 3],
                    'past_exam_count'  => $number % 13,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }

            // Give each newly generated profile a different number of course posts.
            if ($courses->isNotEmpty()) {
                $students = DB::table('campus_students')->where('email', 'like', $prefix.'%@example.invalid')->get();
                foreach ($students as $student) {
                    $postCount = ((int) substr((string) strstr($student->email, '@', true), -4) % 5) + 2;
                    foreach ($courses->take($postCount) as $offset => $courseId) {
                        DB::table('campus_reviews')->insertOrIgnore([
                            'student_id'           => $student->id,
                            'course_id'            => $courseId,
                            'body'                 => 'デモ用の授業口コミです。授業選びの参考にしてください。',
                            'user_type'            => $types[$offset % count($types)],
                            'has_past_exam'        => $offset === 0,
                            'academic_year'        => 2026,
                            'semester'             => 'first',
                            'credit_ease'          => 3,
                            'grade_ease'           => 3,
                            'assignment_lightness' => 3,
                            'satisfaction'         => 4,
                            'past_exam_similarity' => 3,
                            'created_at'           => now(),
                            'updated_at'           => now(),
                        ]);
                    }
                }
            }

            // Keep every demo profile represented in a registered club.
            if ($clubs->isNotEmpty()) {
                $demoStudents = DB::table('campus_students')->where('university', CampusOptions::UNIVERSITY)
                    ->where('email', 'like', 'campus-demo-%@example.invalid')->orderBy('id')->get();
                foreach ($demoStudents as $index => $student) {
                    DB::table('campus_students')->where('id', $student->id)->update([
                        'circle'     => $clubs[$index % $clubs->count()],
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        $this->command->info('Demo students ready: '.$this->countDemoStudents());
    }

    private function countDemoStudents(): int
    {
        return DB::table('campus_students')->where('university', CampusOptions::UNIVERSITY)
            ->where('email', 'like', 'campus-demo-%@example.invalid')->count();
    }
}

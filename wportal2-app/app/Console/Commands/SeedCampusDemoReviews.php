<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Campus\CampusOptions;
use Database\Seeders\CampusDemoReviewsSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class SeedCampusDemoReviews extends Command
{
    protected $signature   = 'campus:seed-demo-reviews
        {--count=100 : Number of additional demo reviews}';

    protected $description = 'Create additional fictional demo reviews for official University of Aizu courses';

    public function handle(): int
    {
        $count             = (int) $this->option('count');

        if ($count < 1 || $count > 10000) {
            $this->error('--count must be between 1 and 10000.');

            return self::FAILURE;
        }

        $selected          = [];

        foreach (CampusDemoReviewsSeeder::COURSES as $externalId => $codes) {
            $teacher = DB::table('campus_teachers')
                ->where('university', CampusOptions::UNIVERSITY)
                ->where('external_teacher_id', (string) $externalId)
                ->first();

            if (! $teacher) {
                throw new RuntimeException(
                    'Official teacher is missing: '.$externalId
                    .'. Run the University of Aizu catalog import first.'
                );
            }

            foreach ($codes as $code) {
                $course      = DB::table('campus_courses')
                    ->where('university', CampusOptions::UNIVERSITY)
                    ->where('academic_year', 2026)
                    ->where('course_code', $code)
                    ->first();

                if (! $course) {
                    throw new RuntimeException(
                        'Official course is missing: '.$code
                    );
                }

                $hasRelation = DB::table('campus_course_teachers')
                    ->where('course_id', $course->id)
                    ->where('teacher_id', $teacher->id)
                    ->where('role', 'instructor')
                    ->exists();

                if (! $hasRelation) {
                    throw new RuntimeException(
                        'Official course/instructor relation missing: '.$code
                    );
                }

                $selected[]  = [
                    'course'  => $course,
                    'teacher' => $teacher,
                ];
            }
        }

        $comments          = [
            '授業内容を確認するために作成した架空の口コミです。説明を聞いた後に復習しながら理解を深める学生を想定しています。',
            '課題量や授業の進め方を確認するためのダミー口コミです。毎週少しずつ時間を確保して取り組む学生を想定しています。',
            '授業検索画面の表示確認用口コミです。興味のあるテーマについて自分でも調べながら受講する架空の学生を想定しています。',
            '試験やレポートの表示確認を目的としたダミー口コミです。授業後にノートを見直して復習する受講パターンを設定しています。',
            '評価画面の動作確認を目的とした架空の口コミです。授業内容について友人と相談しながら学習する設定です。',
            '授業の難易度や満足度を確認するためのサンプル口コミです。講義資料を見ながら自主的に学習する学生を想定しています。',
            '課題や出席方法の画面表示を確認するための架空データです。授業ごとに計画的に取り組む学生を設定しています。',
            '履修検討画面の確認用として作成したダミー口コミです。内容に興味を持って受講している学生を想定しています。',
            '授業評価機能の確認を目的としたサンプルです。講義と課題をバランスよく進める架空の受講パターンです。',
            'レビュー一覧の表示確認に利用するダミー口コミです。授業後に分からない部分を調べ直す学生を想定しています。',
        ];

        $evaluationMethods = [
            ['test'],
            ['report'],
            ['attendance'],
            ['test', 'report'],
            ['test', 'attendance'],
            ['report', 'attendance'],
            ['test', 'report', 'attendance'],
        ];

        $assignmentLoads   = [
            'many',
            'few',
            'none',
        ];

        $remoteLevels      = [
            'none',
            'few',
            'many',
        ];

        $classSizes        = [
            'large',
            'medium',
            'small',
        ];

        $assignmentAmounts = [
            5,
            4,
            3,
            2,
            1,
        ];

        $remotePercentages = [
            0,
            20,
            40,
            60,
            80,
        ];

        $userTypes         = array_keys(CampusOptions::USER_TYPES);
        $ratingKeys        = array_keys(CampusOptions::RATINGS);

        DB::transaction(function () use (
            $count,
            $selected,
            $comments,
            $evaluationMethods,
            $assignmentLoads,
            $remoteLevels,
            $classSizes,
            $assignmentAmounts,
            $remotePercentages,
            $userTypes,
            $ratingKeys
        ): void {
            for ($index = 0; $index < $count; $index++) {
                $selection   = $selected[
                    $index % count($selected)
                ];

                $course      = $selection['course'];

                $number      = $index + 1;

                $email       = sprintf(
                    'campus-demo-extra-review-%03d@example.invalid',
                    $number
                );

                $name        = sprintf(
                    '架空追加学生%03d（デモ）',
                    $number
                );

                $student     = DB::table('campus_students')
                    ->where('email', $email)
                    ->first();

                if (
                    $student
                    && (
                        $student->name          !== $name
                        || $student->university !== CampusOptions::UNIVERSITY
                    )
                ) {
                    throw new RuntimeException(
                        'Demo email is already used by another account: '
                        .$email
                    );
                }

                if (! $student) {
                    $studentId = DB::table('campus_students')
                        ->insertGetId([
                            'email'       => $email,
                            'name'        => $name,
                            'password'    => Hash::make(
                                Str::random(64)
                            ),
                            'university'  => CampusOptions::UNIVERSITY,
                            'faculty'     => 'コンピュータ理工学部',
                            'year'        => 1 + ($index % 4),
                            'circle'      => '',
                            'personality' => null,
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ]);
                } else {
                    $studentId = $student->id;

                    DB::table('campus_students')
                        ->where('id', $studentId)
                        ->update([
                            'circle'     => '',
                            'updated_at' => now(),
                        ]);
                }

                $pattern     = $index % 10;

                $hasPastExam = $index % 2 === 0;

                $data        = [
                    'username'           => $name,
                    'faculty'            => 'コンピュータ理工学部',
                    'department'         => 'コンピュータ理工学科',

                    'user_type'          => $userTypes[
                        $index % count($userTypes)
                    ],

                    'has_past_exam'      => $hasPastExam,

                    'evaluation_methods' => json_encode(
                        $evaluationMethods[
                            $index % count($evaluationMethods)
                        ],
                        JSON_THROW_ON_ERROR
                    ),

                    'assignment_load'    => $assignmentLoads[
                        $index % count($assignmentLoads)
                    ],

                    'remote_level'       => $remoteLevels[
                        $index % count($remoteLevels)
                    ],

                    'materials'          => '【デモ】架空の配布資料'
                        .'（実際の指定教材ではありません）',

                    'materials_url'      => null,

                    'class_size'         => $classSizes[
                        $index % count($classSizes)
                    ],

                    'academic_year'      => 2026,

                    'semester'           => 'first',

                    'attendance_method'  => '【デモ】出席確認方法のサンプル',

                    'assignment_amount'  => $assignmentAmounts[
                            $index % count($assignmentAmounts)
                        ],

                    'remote_percentage'  => $remotePercentages[
                            $index % count($remotePercentages)
                        ],

                    'body'               => '【ダミーデータ・実際の受講評価ではありません】'
                        .$course->name
                        .'：'
                        .$comments[$pattern],

                    'updated_at'         => now(),
                ];

                foreach ($ratingKeys as $axis => $key) {
                    $data[$key] = 1 + (
                        ($index + $axis) % 5
                    );
                }

                DB::table('campus_reviews')->upsert(
                    [
                        $data + [
                            'student_id' => $studentId,
                            'course_id'  => $course->id,
                            'created_at' => now(),
                        ],
                    ],
                    [
                        'student_id',
                        'course_id',
                    ],
                    array_keys($data)
                );
            }
        });

        $pastExamYes       = (int) ceil($count / 2);
        $pastExamNo        = (int) floor($count / 2);

        $this->newLine();

        $this->info(
            'Additional campus demo reviews are ready.'
        );

        $this->newLine();

        $this->line(
            'Reviews: '.$count
        );

        $this->line(
            'Courses: '.count($selected)
        );

        $this->line(
            'Fictional students: '.$count
        );

        $this->line(
            'Past exam available: '.$pastExamYes
        );

        $this->line(
            'Past exam unavailable: '.$pastExamNo
        );

        $this->line(
            'Club membership: none'
        );

        $this->newLine();

        return self::SUCCESS;
    }
}

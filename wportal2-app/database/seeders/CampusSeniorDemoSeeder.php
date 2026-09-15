<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Campus\CampusOptions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class CampusSeniorDemoSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * 20人で合計100口コミ
         *
         * 3件 × 4人 = 12
         * 4件 × 4人 = 16
         * 5件 × 4人 = 20
         * 6件 × 4人 = 24
         * 7件 × 4人 = 28
         *
         * 合計 = 100件
         */
        $reviewCounts      = [
            3, 3, 3, 3,
            4, 4, 4, 4,
            5, 5, 5, 5,
            6, 6, 6, 6,
            7, 7, 7, 7,
        ];

        /*
         * 各先輩が所持している過去問数
         */
        $pastExamCounts    = [
            0, 1, 2, 3,
            4, 5, 6, 7,
            8, 9, 10, 12,
            2, 4, 6, 8,
            3, 5, 7, 11,
        ];

        /*
         * 会津大学2026年度の実在授業から30件使用
         */
        $courses           = DB::table('campus_courses')
            ->where('university', CampusOptions::UNIVERSITY)
            ->where('academic_year', 2026)
            ->whereNotNull('course_code')
            ->orderBy('course_code')
            ->limit(30)
            ->get();

        if ($courses->count() < 10) {
            throw new RuntimeException(
                '2026年度の会津大学授業データが不足しています。'
                .'先に campus:import-u-aizu --year=2026 を実行してください。'
            );
        }

        $comments          = [
            '【ダミーデータ】授業内容は比較的理解しやすく、復習すると理解が深まる想定です。',
            '【ダミーデータ】課題は計画的に進めれば対応できるという架空の口コミです。',
            '【ダミーデータ】講義資料を確認しながら学習する学生を想定しています。',
            '【ダミーデータ】試験前には授業内容を一通り復習する必要があるという設定です。',
            '【ダミーデータ】授業内容に興味を持って受講している架空学生の口コミです。',
            '【ダミーデータ】課題と授業のバランスを考えながら履修する想定です。',
            '【ダミーデータ】授業後に分からなかったところを調べ直す学生を想定しています。',
            '【ダミーデータ】出席と課題を継続して行う学生を想定したサンプルです。',
            '【ダミーデータ】友人と相談しながら学習する架空の受講パターンです。',
            '【ダミーデータ】履修検索・口コミ表示確認のための架空データです。',
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

        $userTypes         = array_keys(CampusOptions::USER_TYPES);
        $ratingKeys        = array_keys(CampusOptions::RATINGS);

        DB::transaction(function () use (
            $reviewCounts,
            $pastExamCounts,
            $courses,
            $comments,
            $evaluationMethods,
            $assignmentLoads,
            $remoteLevels,
            $classSizes,
            $userTypes,
            $ratingKeys
        ): void {
            foreach ($reviewCounts as $studentIndex => $reviewCount) {
                $number  = $studentIndex + 1;

                $email   = sprintf(
                    'campus-senior-demo-%02d@example.invalid',
                    $number
                );

                $name    = sprintf(
                    '架空先輩%02d（デモ）',
                    $number
                );

                $student = DB::table('campus_students')
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
                        'Demo email is already used: '.$email
                    );
                }

                if (! $student) {
                    $studentId = DB::table('campus_students')
                        ->insertGetId([
                            'email'           => $email,
                            'name'            => $name,
                            'password'        => Hash::make(
                                Str::random(64)
                            ),
                            'university'      => CampusOptions::UNIVERSITY,
                            'faculty'         => 'コンピュータ理工学部',

                            /*
                             * 先輩なので2〜4年生
                             */
                            'year'            => 2 + ($studentIndex % 3),

                            /*
                             * 全員サークル非加盟
                             */
                            'circle'          => '',

                            'personality'     => null,

                            /*
                             * 過去問所持数
                             */
                            'past_exam_count' => $pastExamCounts[$studentIndex],

                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ]);
                } else {
                    $studentId = $student->id;

                    DB::table('campus_students')
                        ->where('id', $studentId)
                        ->update([
                            'circle'          => '',
                            'past_exam_count' => $pastExamCounts[$studentIndex],
                            'updated_at'      => now(),
                        ]);
                }

                /*
                 * 学生ごとに3〜7授業へ投稿
                 */
                for (
                    $reviewIndex = 0;
                    $reviewIndex < $reviewCount;
                    $reviewIndex++
                ) {
                    /*
                     * 学生ごとに開始位置を変える。
                     * 同じ学生が同じ授業へ2回投稿しない。
                     */
                    $courseIndex = (
                        $studentIndex * 3
                        + $reviewIndex
                    )                   % $courses->count();

                    $course      = $courses->get($courseIndex);

                    if ($course === null) {
                        throw new RuntimeException(
                            'Demo course could not be resolved at index '.$courseIndex
                        );
                    }

                    /*
                     * 過去問0問の学生は必ずfalse。
                     *
                     * 過去問を持っている学生は
                     * 一部の授業でtrueにする。
                     */
                    $hasPastExam =
                        $pastExamCounts[$studentIndex] > 0
                        && $reviewIndex % 2 === 0;

                    $pattern     =
                        ($studentIndex + $reviewIndex)
                                        % count($comments);

                    $data        = [
                        'username'           => $name,

                        'faculty'            => 'コンピュータ理工学部',

                        'department'         => 'コンピュータ理工学科',

                        'user_type'          => $userTypes[
                            (
                                $studentIndex
                                + $reviewIndex
                            ) % count($userTypes)
                        ],

                        /*
                         * この授業について
                         * 過去問を持っているか
                         */
                        'has_past_exam'      => $hasPastExam,

                        'evaluation_methods' => json_encode(
                            $evaluationMethods[
                                (
                                    $studentIndex
                                    + $reviewIndex
                                )
                                % count(
                                    $evaluationMethods
                                )
                            ],
                            JSON_THROW_ON_ERROR
                        ),

                        'assignment_load'    => $assignmentLoads[
                                (
                                    $studentIndex
                                    + $reviewIndex
                                )
                                % count(
                                    $assignmentLoads
                                )
                            ],

                        'remote_level'       => $remoteLevels[
                                (
                                    $studentIndex
                                    + $reviewIndex
                                )
                                % count(
                                    $remoteLevels
                                )
                            ],

                        'materials'          => '【デモ】架空の教材情報です',

                        'materials_url'      => null,

                        'class_size'         => $classSizes[
                                (
                                    $studentIndex
                                    + $reviewIndex
                                )
                                % count(
                                    $classSizes
                                )
                            ],

                        'academic_year'      => 2026,

                        'semester'           => 'first',

                        'attendance_method'  => '【デモ】架空の出席方法です',

                        'assignment_amount'  => 1 + (
                            (
                                $studentIndex
                                + $reviewIndex
                            ) % 5
                        ),

                        'remote_percentage'  => [0, 20, 40, 60, 80][
                                (
                                    $studentIndex
                                    + $reviewIndex
                                ) % 5
                            ],

                        'body'               => $course->name
                            .'：'
                            .$comments[$pattern],

                        'updated_at'         => now(),
                    ];

                    /*
                     * レーダーチャート用評価。
                     * 1〜5を分散させる。
                     */
                    foreach (
                        $ratingKeys as $axis => $key
                    ) {
                        $data[$key] =
                            1 + (
                                (
                                    $studentIndex
                                    + $reviewIndex
                                    + $axis
                                ) % 5
                            );
                    }

                    /*
                     * 再実行時も重複しない
                     */
                    DB::table('campus_reviews')
                        ->upsert(
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
            }
        });

        $totalReviews      = array_sum(
            $reviewCounts
        );

        $this->command->newLine();

        $this->command->info(
            'Senior demo data ready.'
        );

        $this->command->line(
            'Students: '.count($reviewCounts)
        );

        $this->command->line(
            'Reviews: '.$totalReviews
        );

        $this->command->line(
            'Minimum reviews per student: '
            .min($reviewCounts)
        );

        $this->command->line(
            'Maximum reviews per student: '
            .max($reviewCounts)
        );

        $this->command->line(
            'Minimum past exams: '
            .min($pastExamCounts)
        );

        $this->command->line(
            'Maximum past exams: '
            .max($pastExamCounts)
        );

        $this->command->line(
            'Club membership: none'
        );

        $this->command->newLine();
    }
}

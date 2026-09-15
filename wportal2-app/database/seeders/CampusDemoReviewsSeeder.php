<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Campus\CampusOptions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class CampusDemoReviewsSeeder extends Seeder
{
    public const COURSES = [
        '90117' => ['TE03', 'TE04', 'TE05'],
        '90042' => ['CN02', 'CN04'],
        '90133' => ['OT03-012', 'OT03-021'],
        '90033' => ['SY06'],
        '90048' => ['IT05'],
        '90057' => ['MA10'],
    ];

    public function run(): void
    {
        DB::transaction(function () {
            // Resolve official identifiers instead of environment-specific numeric IDs.
            $selected = [];
            foreach (self::COURSES as $externalId => $codes) {
                $teacher = DB::table('campus_teachers')->where('university', CampusOptions::UNIVERSITY)->where('external_teacher_id', (string) $externalId)->first();
                if (! $teacher) {
                    throw new RuntimeException('Import official 2026 catalog first: teacher '.$externalId.' is missing.');
                }
                foreach ($codes as $code) {
                    $course     = DB::table('campus_courses')->where('university', CampusOptions::UNIVERSITY)->where('academic_year', 2026)->where('course_code', $code)->first();
                    if (! $course || ! DB::table('campus_course_teachers')->where('course_id', $course->id)->where('teacher_id', $teacher->id)->where('role', 'instructor')->exists()) {
                        throw new RuntimeException('Official course/instructor relation missing: '.$code);
                    }
                    $selected[] = $course;
                }
                $this->command->line($teacher->name.': '.implode(', ', $codes));
            }
            $comments = [
                '予習してから取り組む場面を想定したサンプルです。図や具体例を見ながら理解を深める、という架空の感想を設定しています。',
                '復習と課題の進め方を確認するためのサンプルです。少しずつ時間を確保して取り組む、という架空の受講パターンを設定しています。',
                '授業選びの画面を確認するためのサンプルです。興味のある内容を自分でも調べる、という架空の感想を設定しています。',
            ];
            foreach ($selected as $index => $course) {
                for ($offset = 0; $offset < 3; $offset++) {
                    $number  = $index * 3 + $offset + 1;
                    $email   = sprintf('campus-demo-review-%02d@example.invalid', $number);
                    $name    = sprintf('架空学生%02d（デモ）', $number);
                    $student = DB::table('campus_students')->where('email', $email)->first();
                    if ($student && ($student->name !== $name || $student->university !== CampusOptions::UNIVERSITY)) {
                        throw new RuntimeException('Demo email is already used by a different account: '.$email);
                    }
                    if (! $student) {
                        $id = DB::table('campus_students')->insertGetId([
                            'email'      => $email, 'name' => $name, 'password' => Hash::make(Str::random(64)),
                            'university' => CampusOptions::UNIVERSITY, 'faculty' => 'コンピュータ理工学部',
                            'year'       => 1 + $number % 4, 'circle' => '', 'personality' => null,
                            'created_at' => now(), 'updated_at' => now(),
                        ]);
                    } else {
                        $id = $student->id;
                        DB::table('campus_students')->where('id', $id)->update(['circle' => '']);
                    }
                    $data    = [
                        'username'          => $name, 'faculty' => 'コンピュータ理工学部', 'department' => 'コンピュータ理工学科',
                        'user_type'         => array_keys(CampusOptions::USER_TYPES)[$offset], 'has_past_exam' => $offset === 0,
                        'test_weight'       => [60, 0, 50][$offset], 'report_weight' => [0, 80, 0][$offset], 'attendance_weight' => [0, 20, 50][$offset],
                        'assignment_load'   => ['many', 'few', 'none'][$offset], 'remote_level' => ['none', 'few', 'many'][$offset],
                        'materials'         => '【デモ】架空の配布資料（実際の指定教材ではありません）',
                        'class_size'        => ['over_40', 'range_20_39', 'under_20'][$offset], 'academic_year' => 2026, 'semester' => 'first',
                        'attendance_method' => '【デモ】出席確認のサンプル', 'assignment_amount' => [5, 2, 1][$offset],
                        'remote_percentage' => [0, 20, 80][$offset],
                        'body'              => '【ダミーデータ・実際の受講評価ではありません】'.$course->name.'：'.$comments[$offset],
                        'updated_at'        => now(),
                    ];
                    foreach (array_keys(CampusOptions::RATINGS) as $axis => $key) {
                        $data[$key] = 1 + ($index + $offset + $axis) % 5;
                    }
                    DB::table('campus_reviews')->upsert([
                        $data + ['student_id' => $id, 'course_id' => $course->id, 'created_at' => now()],
                    ], ['student_id', 'course_id'], array_keys($data));
                }
            }
        });
        $this->command->info('Demo reviews ready: 6 teachers / 10 courses / 30 reviews / 30 fictional students (no club membership).');
    }
}

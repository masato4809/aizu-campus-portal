<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Campus\CampusOptions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampusTeacher28ReviewsSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = DB::table('campus_teachers')->where('id', 28)->where('university', CampusOptions::UNIVERSITY)->first();
        $students = DB::table('campus_students')->where('university', CampusOptions::UNIVERSITY)
            ->where('email', 'like', 'campus-demo-%@example.invalid')->orderBy('id')->get();
        if (! $teacher || $students->count() < 40) {
            $this->command?->error('先生ID28またはデモ学生が見つかりません。');
            return;
        }
        foreach ($students->take(40) as $index => $student) {
            DB::table('campus_teacher_reviews')->updateOrInsert(
                ['teacher_id' => $teacher->id, 'student_id' => $student->id],
                ['rating' => ($index % 5) + 1, 'body' => 'ファン トゥアン アン先生は説明が丁寧で、質問しやすい先生です。', 'created_at' => now(), 'updated_at' => now()]
            );
        }
        $this->command?->info('ファン トゥアン アン先生に口コミを40件追加しました。');
    }
}

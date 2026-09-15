<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Campus\CampusOptions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampusTeacherReviewsDemoSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = DB::table('campus_teachers')->where('university', CampusOptions::UNIVERSITY)->orderBy('id')->get();
        $students = DB::table('campus_students')->where('university', CampusOptions::UNIVERSITY)
            ->where('email', 'like', 'campus-demo-%@example.invalid')->orderBy('id')->get();
        if ($teachers->isEmpty() || $students->isEmpty()) {
            $this->command?->warn('教員またはデモ学生がいないため、教授口コミを作成できません。');
            return;
        }

        $created = 0;
        foreach (range(0, 59) as $index) {
            $teacher = $teachers[$index % $teachers->count()];
            $student = $students[$index % $students->count()];
            DB::table('campus_teacher_reviews')->updateOrInsert(
                ['teacher_id' => $teacher->id, 'student_id' => $student->id],
                [
                    'rating' => ($index % 5) + 1,
                    'body' => sprintf('%s先生の授業は分かりやすく、質問にも丁寧に答えてくれました。', $teacher->name),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
            $created++;
        }

        $this->command?->info('教授口コミを'.$created.'件登録しました。');
    }
}

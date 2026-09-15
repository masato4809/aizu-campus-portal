<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CampusSeniorReviewVariationSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $students  = collect();
            for ($n = 1; $n <= 20; $n++) {
                $student = DB::table('campus_students')->where('email', sprintf('campus-senior-demo-%02d@example.invalid', $n))
                    ->where('name', sprintf('架空先輩%02d（デモ）', $n))->where('university', '会津大学')->first();
                if (! $student) {
                    throw new RuntimeException('All 20 demo seniors must exist.');
                }
                $students->push($student);
            }
            $reviews   = DB::table('campus_reviews')->whereIn('student_id', $students->pluck('id'))->get();
            if ($reviews->count() !== 100 || $reviews->contains(fn ($r) => ! str_contains($r->body, 'ダミーデータ'))) {
                throw new RuntimeException('Expected exactly 100 labelled demo reviews; no changes made.');
            }
            $targets   = collect([3, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 6, 6, 6, 6, 7, 7, 7, 7])->shuffle()->values();
            foreach ($students as $index => $recipient) {
                while ($reviews->where('student_id', $recipient->id)->count() < $targets[$index]) {
                    $owned               = $reviews->where('student_id', $recipient->id)->pluck('course_id')->all();
                    $movable             = null;
                    foreach ($students as $donorIndex => $donor) {
                        if ($reviews->where('student_id', $donor->id)->count() <= $targets[$donorIndex]) {
                            continue;
                        }
                        $movable = $reviews->where('student_id', $donor->id)->first(fn ($r) => ! in_array($r->course_id, $owned, true));
                        if ($movable) {
                            break;
                        }
                    }
                    if (! $movable) {
                        throw new RuntimeException('Cannot redistribute without duplicate courses; rolled back.');
                    }
                    DB::table('campus_reviews')->where('id', $movable->id)->update(['student_id' => $recipient->id, 'username' => $recipient->name]);
                    $movable->student_id = $recipient->id;
                }
            }
            $withExams = $reviews->shuffle()->take(80)->pluck('id')->all();
            DB::table('campus_reviews')->whereIn('id', $reviews->pluck('id'))->update(['has_past_exam' => false]);
            DB::table('campus_reviews')->whereIn('id', $withExams)->update(['has_past_exam' => true]);
            foreach ($students as $student) {
                $coursesWithExams = $reviews->where('student_id', $student->id)->whereIn('id', $withExams)->count();
                DB::table('campus_students')->where('id', $student->id)->update([
                    'past_exam_count' => $coursesWithExams === 0 ? 0 : random_int($coursesWithExams, 12),
                ]);
            }
        });
        $this->command->info('100 demo reviews: 80 with past exams; 3–7 randomly assigned posts per senior.');
    }
}

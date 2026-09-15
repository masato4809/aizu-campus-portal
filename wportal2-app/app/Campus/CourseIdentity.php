<?php

declare(strict_types=1);

namespace App\Campus;

use App\Campus\Import\NameMatcher;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CourseIdentity
{
    /** @param array<string, mixed> $data */
    public function resolve(\stdClass $student, array $data): int
    {
        if (! empty($data['course_id'])) {
            $course = DB::table('campus_courses')->where('university', $student->university)->where('id', $data['course_id'])->first();
            if (! $course) {
                throw ValidationException::withMessages(['course_id' => '授業を確認してください。']);
            }

            return $course->id;
        }
        $official = DB::table('campus_courses')->where('university', $student->university)->where('academic_year', $data['academic_year'])
            ->where('name', $data['course'])->get();
        $matching = $official->filter(function ($course) use ($data) {
            $names = [$course->professor];
            foreach (json_decode($course->offerings ?? '[]', true) as $offering) {
                foreach ($offering['teachers'] as $teacher) {
                    $names[] = $teacher['name'];
                }
            }

            return in_array(NameMatcher::compact($data['professor']), array_map(NameMatcher::compact(...), $names), true);
        });
        if ($matching->count() === 1) {
            return $matching->sole()->id;
        }
        if ($official->isNotEmpty()) {
            throw ValidationException::withMessages(['course_id' => '公式授業のIDを指定してください（同名科目や担当者の確認が必要です）。']);
        }
        $identity = ['university' => $student->university, 'legacy_key' => hash('sha256', $data['course']."\0".$data['professor'])];
        DB::table('campus_courses')->insertOrIgnore($identity + ['name' => $data['course'], 'professor' => $data['professor'], 'created_at' => now(), 'updated_at' => now()]);

        return (int) DB::table('campus_courses')->where($identity)->value('id');
    }
}

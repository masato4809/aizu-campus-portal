<?php

declare(strict_types=1);

namespace App\Campus;

use Illuminate\Support\Facades\DB;

class ReviewWriter
{
    /** @param array<string, mixed> $data */
    public function save(\stdClass $student, array $data): int
    {
        return DB::transaction(function () use ($student, $data): int {
            $courseId                   = app(CourseIdentity::class)->resolve($student, $data);
            unset($data['course'], $data['professor'], $data['course_id']);
            $data['body']               = $data['body']      ?? '';
            $data['materials']          = $data['materials'] ?? null;
            $data['username']           = $student->name;
            // The author always comes from the authenticated session.
            DB::table('campus_reviews')->upsert([
                $data + ['student_id' => $student->id, 'course_id' => $courseId, 'created_at' => now(), 'updated_at' => now()],
            ], ['student_id', 'course_id'], array_merge(array_keys($data), ['updated_at']));

            return $courseId;
        });
    }
}

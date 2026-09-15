<?php

declare(strict_types=1);

namespace App\Campus;

use Illuminate\Support\Facades\DB;

class TeacherDetails
{
    /** @return array<string, mixed> */
    public function get(\stdClass $student, int $teacherId): array
    {
        $teacher        = DB::table('campus_teachers')->where('university', $student->university)->where('id', $teacherId)->first();
        abort_if($teacher === null, 404);
        $courses        = DB::table('campus_courses')->where('university', $student->university)
            ->whereIn('id', DB::table('campus_course_teachers')->select('course_id')->where('teacher_id', $teacherId)->where('role', 'instructor'))
            ->select('id', 'course_code', 'name', 'name_en', 'academic_year', 'semester', 'syllabus_url')->orderByDesc('academic_year')->orderBy('course_code')->get();
        // IN avoids counting a review twice when a teacher has several roles.
        $reviews        = DB::table('campus_reviews')->whereIn('course_id', $courses->pluck('id'));
        $averages       = $counts = [];
        foreach (CampusOptions::RATINGS as $key => $label) {
            $average        = (clone $reviews)->avg($key);
            $averages[$key] = $average === null ? null : round((float) $average, 2);
            $counts[$key]   = (clone $reviews)->whereNotNull($key)->count();
        }

        $teacherReviews = DB::table('campus_teacher_reviews')->where('teacher_id', $teacherId);
        $teacherAverage = (clone $teacherReviews)->avg('rating');
        $comments       = DB::table('campus_teacher_reviews as r')
            ->join('campus_students as s', 's.id', '=', 'r.student_id')->where('r.teacher_id', $teacherId)
            ->select('r.id', 'r.rating', 'r.body', 'r.created_at', 'r.updated_at', 's.name as author', 's.id as author_id')
            ->orderByDesc('r.updated_at')->orderByDesc('r.id')->paginate(20, ['*'], 'comments_page');

        return [
            'comments'                    => $comments,
            'teacher_rating_average'      => $teacherAverage === null ? null : round((float) $teacherAverage, 2),
            'teacher_review_count'        => (clone $teacherReviews)->count(),
            'teacher'                     => $teacher,
            'courses'                     => $courses,
            'laboratory'                  => ['name' => $teacher->laboratory_name, 'url' => $teacher->laboratory_url],
            'course_review_averages'      => $averages,
            'course_review_rating_counts' => $counts,
            'course_review_count'         => (clone $reviews)->count(),
        ];
    }
}

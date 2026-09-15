<?php

declare(strict_types=1);

namespace App\Campus;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CourseCatalog
{
    /** @return array{courses: Collection<int, \stdClass>, reviews: Collection<int, \stdClass>} */
    public function get(\stdClass $me, string $search = '', ?int $courseId = null): array
    {
        $courses  = DB::table('campus_courses')->where('university', $me->university)
            ->when($courseId !== null, fn ($q) => $q->where('id', $courseId))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $term = '%'.mb_substr($search, 0, 120).'%';
                    $q->where('name', 'like', $term)->orWhere('professor', 'like', $term)
                        ->orWhere('name_en', 'like', $term)->orWhere('course_code', 'like', $term)
                        ->orWhereExists(function ($teachers) use ($term) {
                            $teachers->selectRaw('1')->from('campus_course_teachers as ct')
                                ->join('campus_teachers as t', 't.id', '=', 'ct.teacher_id')
                                ->whereColumn('ct.course_id', 'campus_courses.id')
                                ->where(function ($names) use ($term) {
                                    $names->where('t.name', 'like', $term)->orWhere('t.name_en', 'like', $term);
                                });
                        });
                });
            })->orderBy('name')->get();
        $reviews  = DB::table('campus_reviews as r')->join('campus_students as s', 's.id', '=', 'r.student_id')
            ->join('campus_courses as c', 'c.id', '=', 'r.course_id')->where('c.university', $me->university)
            ->select('r.*', 's.name as author', 's.circle as author_circle', 's.personality as author_personality', 's.year', 'c.name as course_name')
            ->selectSub(DB::table('campus_review_helpful_votes as hv')->selectRaw('COUNT(*)')->whereColumn('hv.review_id', 'r.id'), 'helpful_count')
            ->selectSub(DB::table('campus_review_helpful_votes as mine')->selectRaw('COUNT(*)')->whereColumn('mine.review_id', 'r.id')->where('mine.student_id', $me->id), 'helpful_by_me')
            ->orderByDesc('r.created_at')->get();
        $teachers = DB::table('campus_course_teachers as ct')
            ->join('campus_teachers as t', 't.id', '=', 'ct.teacher_id')
            ->whereIn('ct.course_id', $courses->pluck('id'))->select('t.*', 'ct.course_id', 'ct.role')
            ->get()->groupBy('course_id');
        foreach ($courses as $course) {
            $course->teachers      = $teachers->get($course->id, collect());
            $course->reviews       = $reviews->where('course_id', $course->id)->values();
            $course->review_count  = $course->reviews->count();
            $course->averages      = [];
            $course->rating_counts = [];
            foreach (CampusOptions::RATINGS as $key => $label) {
                $course->averages[$key]      = (float) ($course->reviews->avg($key) ?? 0);
                $course->rating_counts[$key] = $course->reviews->whereNotNull($key)->count();
            }
        }

        return compact('courses', 'reviews');
    }
}

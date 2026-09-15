<?php

declare(strict_types=1);

namespace App\Http\Controllers\Campus;

use App\Campus\CampusOptions;
use App\Campus\CourseCatalog;
use App\Campus\CurrentStudent;
use App\Campus\StudentMatches;
use App\Campus\TeacherDetails;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogPageController extends Controller
{
    public function professor(Request $request, int $teacher, CurrentStudent $students, TeacherDetails $details): View
    {
        $me   = $students->requireStudent($request);
        $data = $details->get($me, $teacher);
        $data['comments']->withPath('/campus/professors/'.$teacher);

        return view('campus.pages.professor-detail', $data + ['page' => 'professors', 'me' => $me, 'ratings' => CampusOptions::RATINGS,
            'myReview'                                               => DB::table('campus_teacher_reviews')->where('teacher_id', $teacher)->where('student_id', $me->id)->first()]);
    }

    public function course(Request $request, int $course, CurrentStudent $students, CourseCatalog $catalog, StudentMatches $studentMatches): View
    {
        $me            = $students->requireStudent($request);
        $validated     = $request->validate([
            'user_type' => ['nullable', 'string', 'in:'.implode(',', array_keys(CampusOptions::USER_TYPES))],
        ]);
        $selectedType  = $validated['user_type'] ?? null;
        $item          = $catalog->get($me, '', $course)['courses']->first();
        abort_if($item === null, 404);
        $matchScores   = $studentMatches->get($me)->pluck('score', 'id');
        $item->reviews = $item->reviews->map(function ($review) use ($matchScores) {
            $review->match_score = $matchScores->get($review->student_id);

            return $review;
        })->sortByDesc(fn ($review) => $review->match_score ?? -1)->values();
        if ($selectedType !== null) {
            $item->reviews      = $item->reviews->where('user_type', $selectedType)->values();
            $item->review_count = $item->reviews->count();
            foreach (CampusOptions::RATINGS as $key => $label) {
                $item->averages[$key]      = (float) ($item->reviews->avg($key) ?? 0);
                $item->rating_counts[$key] = $item->reviews->whereNotNull($key)->count();
            }
        }

        return view('campus.pages.course-detail', ['page' => 'courses', 'me' => $me, 'ratings' => CampusOptions::RATINGS, 'course' => $item, 'userTypes' => CampusOptions::USER_TYPES, 'selectedUserType' => $selectedType]);
    }

    public function student(Request $request, int $student, CurrentStudent $students, CourseCatalog $catalog): View
    {
        $me      = $students->requireStudent($request);
        $profile = DB::table('campus_students')->where('university', $me->university)->where('id', $student)
            ->select('id', 'name', 'faculty', 'year', 'circle', 'personality', 'goal_orientation', 'past_exam_count')->first();
        abort_if($profile === null, 404);
        $reviews = $catalog->get($me)['reviews']->where('student_id', $student)->values();

        return view('campus.pages.student-detail', ['page' => 'matches', 'me' => $me, 'profile' => $profile, 'reviews' => $reviews]);
    }
}

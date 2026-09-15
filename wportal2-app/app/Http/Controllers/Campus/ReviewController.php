<?php

declare(strict_types=1);

namespace App\Http\Controllers\Campus;

use App\Campus\CurrentStudent;
use App\Campus\ReviewWriter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Campus\StoreReviewRequest;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, CurrentStudent $currentStudent, ReviewWriter $writer): RedirectResponse
    {
        $courseId = $writer->save($currentStudent->requireStudent($request), $request->validated());

        return redirect('/campus/review/thanks')->with('posted_course_id', $courseId);
    }

    public function thanks(\Illuminate\Http\Request $request, CurrentStudent $students): \Illuminate\Contracts\View\View|RedirectResponse
    {
        $me       = $students->requireStudent($request);
        $courseId = $request->session()->get('posted_course_id');
        if (! $courseId) {
            return redirect('/campus');
        }

        return view('campus.pages.review-thanks', ['page' => 'courses', 'me' => $me, 'courseId' => $courseId]);
    }
}

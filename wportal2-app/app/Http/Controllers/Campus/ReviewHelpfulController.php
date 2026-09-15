<?php

declare(strict_types=1);

namespace App\Http\Controllers\Campus;

use App\Campus\CurrentStudent;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewHelpfulController extends Controller
{
    public function store(Request $request, int $review, CurrentStudent $students): JsonResponse|RedirectResponse
    {
        $student = $students->requireStudent($request);
        $data    = $request->validate(['helpful' => 'required|boolean']);
        $result  = DB::transaction(function () use ($review, $student, $data) {
            $item     = DB::table('campus_reviews')->where('id', $review)->lockForUpdate()->first();
            abort_if($item === null, 404);
            abort_unless(DB::table('campus_courses')->where('id', $item->course_id)->where('university', $student->university)->exists(), 404);
            abort_if((int) $item->student_id === (int) $student->id, 403, '自分の口コミには投票できません。');
            $identity = ['review_id' => $review, 'student_id' => $student->id];
            if ((bool) $data['helpful']) {
                DB::table('campus_review_helpful_votes')->insertOrIgnore($identity + ['created_at' => now(), 'updated_at' => now()]);
            } else {
                DB::table('campus_review_helpful_votes')->where($identity)->delete();
            }

            return ['course_id' => $item->course_id, 'helpful' => (bool) $data['helpful'], 'helpful_count' => DB::table('campus_review_helpful_votes')->where('review_id', $review)->count()];
        });
        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return redirect('/campus/courses/'.$result['course_id'].'#review-'.$review)
            ->with('status', $result['helpful'] ? '「役に立った」を保存しました。' : '「役に立った」を取り消しました。');
    }
}

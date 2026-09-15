<?php

declare(strict_types=1);

namespace App\Http\Controllers\Campus;

use App\Campus\CurrentStudent;
use App\Campus\TeacherDetails;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function show(Request $request, int $teacher, CurrentStudent $students, TeacherDetails $details): JsonResponse
    {
        return response()->json($details->get($students->requireStudent($request), $teacher));
    }

    public function store(Request $request, int $teacher, CurrentStudent $students): JsonResponse|RedirectResponse
    {
        $student = $students->requireStudent($request);
        abort_unless(DB::table('campus_teachers')->where('university', $student->university)->where('id', $teacher)->exists(), 404);
        $data    = $request->validate(['rating' => 'required|integer|between:1,5', 'body' => 'required|string|max:500']);
        DB::table('campus_teacher_reviews')->upsert([
            $data + ['teacher_id' => $teacher, 'student_id' => $student->id, 'created_at' => now(), 'updated_at' => now()],
        ], ['teacher_id', 'student_id'], ['rating', 'body', 'updated_at']);

        if (! $request->expectsJson()) {
            return redirect('/campus/professors/'.$teacher)->with('status', '先生へのコメントと評価を保存しました。');
        }

        return response()->json(['message' => '先生へのコメントと評価を保存しました。']);
    }
}

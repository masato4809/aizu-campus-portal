<?php

declare(strict_types=1);

namespace App\Http\Controllers\Campus;

use App\Campus\CampusOptions;
use App\Campus\CurrentStudent;
use App\Campus\PersonalityQuiz;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DiagnosisController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $me      = app(CurrentStudent::class)->requireStudent($request);
        $rules   = ['goal_orientation' => ['required', Rule::in(array_keys(CampusOptions::GOAL_ORIENTATIONS))]];
        foreach (PersonalityQuiz::QUESTIONS as $i => $question) {
            $rules['q'.($i + 1)] = 'required|in:a,b';
        }
        $data    = $request->validate($rules);
        $answers = [];
        foreach (PersonalityQuiz::QUESTIONS as $i => $question) {
            $answers[$i + 1] = $data['q'.($i + 1)];
        }

        DB::table('campus_students')->where('id', $me->id)->update([
            'personality'      => app(PersonalityQuiz::class)->score($answers),
            'goal_orientation' => $data['goal_orientation'],
            'updated_at'       => now(),
        ]);

        return redirect('/campus/profile')->with('status', '診断結果を保存しました。');
    }
}

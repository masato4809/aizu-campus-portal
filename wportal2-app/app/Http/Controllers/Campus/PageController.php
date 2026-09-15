<?php

declare(strict_types=1);

namespace App\Http\Controllers\Campus;

use App\Campus\CampusOptions;
use App\Campus\ClubCatalog;
use App\Campus\ClubFeed;
use App\Campus\CourseCatalog;
use App\Campus\CurrentStudent;
use App\Campus\PersonalityQuiz;
use App\Campus\StudentMatches;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    public function index(Request $request, string $page = 'courses'): View|RedirectResponse
    {
        $me   = app(CurrentStudent::class)->get($request);
        if (! $me && ! in_array($page, ['login', 'register'], true)) {
            return redirect('/campus/login');
        }

        $data = [
            'page'              => $page,
            'me'                => $me,
            'clubOptions'       => app(ClubCatalog::class)->all(CampusOptions::UNIVERSITY),
            'ratings'           => CampusOptions::RATINGS,
            'types'             => CampusOptions::TYPES,
            'userTypes'         => CampusOptions::USER_TYPES,
            'evaluationMethods' => CampusOptions::EVALUATION_METHODS,
            'levels'            => CampusOptions::LEVELS,
            'classSizes'        => CampusOptions::CLASS_SIZES,
            'semesters'         => CampusOptions::SEMESTERS,
            'departments'       => CampusOptions::DEPARTMENTS,
        ];
        if ($me) {
            $clubTab = $request->input('tab') === 'similarity' ? 'similarity' : 'reviews';
            $data += match ($page) {
                'professors'            => ['teachers' => DB::table('campus_teachers')->where('university', $me->university)
                    ->when($request->filled('q'), function ($query) use ($request) {
                        $term = '%'.mb_substr((string) $request->input('q'), 0, 120).'%';
                        $query->where(fn ($names) => $names->where('name', 'like', $term)->orWhere('name_en', 'like', $term)->orWhere('research_field', 'like', $term));
                    })->orderBy('name')->orderBy('id')->paginate(24)->withQueryString()],
                'courses'               => ['courseOptions' => DB::table('campus_courses')->where('university', $me->university)->orderByDesc('academic_year')->orderBy('name')->select('id', 'name', 'name_en', 'course_code', 'professor', 'academic_year')->get()],
                'profile'               => [
                    'pastExamCourses'  => DB::table('campus_reviews as r')->join('campus_courses as c', 'c.id', '=', 'r.course_id')
                        ->where('r.student_id', $me->id)->where('r.has_past_exam', true)->where('c.university', $me->university)
                        ->select('c.id', 'c.name', 'c.course_code', 'c.academic_year')->distinct()->orderBy('c.name')->get(),
                    'typeLabels'       => CampusOptions::TYPE_LABELS,
                    'goalOrientations' => CampusOptions::GOAL_ORIENTATIONS,
                ],
                'matches'               => app(CourseCatalog::class)->get($me) + ['matches' => app(StudentMatches::class)->get($me)],
                'clubs'                 => ['clubs' => app(ClubFeed::class)->get($me, $clubTab), 'clubTab' => $clubTab],
                'diagnosis'             => ['questions' => PersonalityQuiz::QUESTIONS, 'goalOrientations' => CampusOptions::GOAL_ORIENTATIONS],
                default                 => [],
            };
        }

        if ($me && $page === 'matches' && isset($data['reviews'])) {
            $data['reviewsByStudent'] = $data['reviews']->groupBy('student_id');
        }

        if ($me && $page === 'courses') {
            $search            = trim((string) $request->input('q', ''));
            $data['hasSearch'] = $search !== '';
            $data += $search             !== ''
                ? app(CourseCatalog::class)->get($me, $search)
                : ['courses' => collect()];

            $courses           = $data['courses'] ?? collect();
            if (! is_iterable($courses)) {
                $courses = collect();
            }

            $matchScores       = app(StudentMatches::class)->get($me)->pluck('score', 'id');
            foreach ($courses as $course) {
                $course->reviews = $course->reviews->map(function ($review) use ($matchScores) {
                    $review->match_score = $matchScores->get($review->student_id);

                    return $review;
                })->sortByDesc(fn ($review) => $review->match_score ?? -1)->values();
            }
        }

        $view = match ($page) {
            'courses'    => 'campus.pages.courses',
            'professors' => 'campus.pages.professors',
            'matches'    => 'campus.pages.matches',
            'clubs'      => 'campus.pages.clubs',
            'profile'    => 'campus.pages.profile',
            'diagnosis'  => 'campus.pages.diagnosis',
            'login'      => 'campus.pages.login',
            'register'   => 'campus.pages.register',
            default      => abort(404),
        };

        return view($view, $data);
    }
}

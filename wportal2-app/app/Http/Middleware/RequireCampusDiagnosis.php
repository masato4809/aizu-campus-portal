<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Campus\CampusOptions;
use App\Campus\CurrentStudent;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireCampusDiagnosis
{
    public static function complete(\stdClass $student): bool
    {
        return in_array($student->personality ?? null, CampusOptions::TYPES, true)
            && isset(CampusOptions::GOAL_ORIENTATIONS[$student->goal_orientation ?? '']);
    }

    public function handle(Request $request, Closure $next): Response
    {
        $student = app(CurrentStudent::class)->get($request);
        if ($student && ! self::complete($student) && ! $request->is('campus/diagnosis', 'campus/logout', 'campus/login')) {
            return redirect('/campus/diagnosis')->with('status', '利用を始める前に、性格・学び方診断の全項目に回答してください。');
        }

        return $next($request);
    }
}

<?php

declare(strict_types=1);

namespace App\Campus;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StudentMatches
{
    /** @return Collection<int, \stdClass> */
    public function get(\stdClass $me): Collection
    {
        $matches = DB::table('campus_students')->where('university', $me->university)->where('year', '>', $me->year)
            ->select('id', 'name', 'faculty', 'year', 'circle', 'personality', 'past_exam_count', 'goal_orientation')->get()->map(function ($senior) use ($me) {
                $senior->reasons = [];
                $senior->score   = 0;
                if (in_array($me->personality ?? null, CampusOptions::TYPES, true) && in_array($senior->personality, CampusOptions::TYPES, true)) {
                    $axes = 0;
                    for ($i = 0; $i < 3; $i++) {
                        $axes += $me->personality[$i] === $senior->personality[$i] ? 1 : 0;
                    }
                    $senior->score += $axes * 20;
                    if ($axes > 0) {
                        $senior->reasons[] = $axes === 3 ? '同じ性格タイプ（+60）' : '性格の'.$axes.'/3軸が共通（+'.($axes * 20).'）';
                    }
                }
                $mine            = $me->goal_orientation ?? null;
                $theirs          = $senior->goal_orientation;
                if (isset(CampusOptions::GOAL_ORIENTATIONS[$mine ?? ''], CampusOptions::GOAL_ORIENTATIONS[$theirs ?? ''])) {
                    $points = $mine === $theirs ? 20 : (($mine === 'balanced' || $theirs === 'balanced') ? 10 : 0);
                    $senior->score += $points;
                    if ($points > 0) {
                        $senior->reasons[] = '学習目標'.($points === 20 ? 'が同じ' : 'が近い').'（+'.$points.'）';
                    }
                }
                foreach (['faculty' => ['同じ学部', 15], 'circle' => ['同じサークル', 5]] as $field => [$label, $points]) {
                    if (filled($me->$field ?? null) && $me->$field === $senior->$field) {
                        $senior->score += $points;
                        $senior->reasons[] = $label.'（+'.$points.'）';
                    }
                }

                return $senior;
            })->sortByDesc('score')->take(20)->values();

        return $matches;
    }
}

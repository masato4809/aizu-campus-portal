<?php

declare(strict_types=1);

namespace App\Campus;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CurrentStudent
{
    public function requireStudent(Request $request): \stdClass
    {
        $student = $this->get($request);
        abort_if($student === null, 403);

        return $student;
    }

    public function get(Request $request): ?\stdClass
    {
        return DB::table('campus_students')
            ->select('id', 'name', 'university', 'faculty', 'year', 'circle', 'personality', 'goal_orientation')
            ->where('id', $request->session()->get('campus_student_id'))->first();
    }
}

<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Campus\CampusOptions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CampusSeniorProfilesSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $clubs    = DB::table('campus_club_catalog')->where('university', CampusOptions::UNIVERSITY)->pluck('name')->shuffle()->values();
            if ($clubs->isEmpty()) {
                throw new RuntimeException('Register the official club catalog first.');
            }
            $students = [];
            for ($number = 1; $number <= 20; $number++) {
                $student    = DB::table('campus_students')->where('email', sprintf('campus-senior-demo-%02d@example.invalid', $number))
                    ->where('name', sprintf('架空先輩%02d（デモ）', $number))->where('university', CampusOptions::UNIVERSITY)->first();
                if (! $student) {
                    throw new RuntimeException('Create all 20 demo seniors first.');
                }
                $students[] = $student;
            }
            $shuffled = collect($students)->shuffle()->values();
            foreach ($shuffled as $index => $student) {
                DB::table('campus_students')->where('id', $student->id)->update([
                    'circle'           => $index < 10 ? '' : $clubs[($index - 10) % $clubs->count()],
                    'personality'      => CampusOptions::TYPES[$index % count(CampusOptions::TYPES)],
                    'goal_orientation' => array_keys(CampusOptions::GOAL_ORIENTATIONS)[$index % 3],
                    'updated_at'       => now(),
                ]);
            }
        });
        $this->command->info('Updated 20 demo seniors: 10 club members, 10 non-members; varied diagnosis profiles. Reviews and past exam counts preserved.');
    }
}

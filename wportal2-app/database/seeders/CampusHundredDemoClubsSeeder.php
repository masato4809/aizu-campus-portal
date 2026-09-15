<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CampusHundredDemoClubsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $students = DB::table('campus_students')->where('university', '会津大学')
                ->where('email', 'like', 'campus-demo-extra-review-%@example.invalid')->get()
                ->filter(fn ($s) => preg_match('/^campus-demo-extra-review-\d+@example\.invalid$/', $s->email) === 1)->shuffle()->values();
            $clubs    = DB::table('campus_club_catalog')->where('university', '会津大学')->pluck('name')->shuffle()->values();
            if ($students->count() !== 100 || $clubs->isEmpty()) {
                throw new RuntimeException('Expected 100 extra demo students and registered clubs.');
            }
            foreach ($students as $index => $student) {
                DB::table('campus_students')->where('id', $student->id)->update([
                    'circle'     => $index < 85 ? $clubs[$index % $clubs->count()] : '',
                    'updated_at' => now(),
                ]);
            }
        });
        $this->command->info('100 demo students: 85 club members, 15 non-members.');
    }
}

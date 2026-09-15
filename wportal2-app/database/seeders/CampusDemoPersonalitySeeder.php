<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Campus\CampusOptions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampusDemoPersonalitySeeder extends Seeder
{
    public function run(): void
    {
        $count = DB::transaction(function () {
            $students = DB::table('campus_students')->where('university', CampusOptions::UNIVERSITY)
                ->where('email', 'like', '%@example.invalid')->where('name', 'like', '%（デモ）')
                ->where(fn ($q) => $q->whereNull('personality')->orWhere('personality', ''))
                ->orderBy('id')->get();
            foreach ($students as $index => $student) {
                DB::table('campus_students')->where('id', $student->id)->update([
                    'personality' => CampusOptions::TYPES[$index % count(CampusOptions::TYPES)], 'updated_at' => now(),
                ]);
            }

            return $students->count();
        });
        $this->command->info('Assigned personality types to '.$count.' demo students.');
    }
}

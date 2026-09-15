<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Campus\CampusOptions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampusClubDemoPostsSeeder extends Seeder
{
    public function run(): void
    {
        $clubs = DB::table('campus_club_catalog')->where('university', CampusOptions::UNIVERSITY)->orderBy('id')->get();
        foreach ($clubs as $club) {
            $author = DB::table('campus_students')->where('university', CampusOptions::UNIVERSITY)
                ->where('circle', $club->name)->orderBy('id')->first();
            if (! $author) {
                continue;
            }
            $body   = sprintf('%sのデモ紹介です。初心者も歓迎しています。活動日や見学については気軽にお問い合わせください。', $club->name);
            DB::table('campus_clubs')->updateOrInsert(
                ['student_id' => $author->id, 'name' => $club->name, 'body' => $body],
                ['updated_at' => now(), 'created_at' => now()],
            );
        }

        $this->command->info('Demo club posts ready: '.$clubs->count());
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Campus;

use Database\Seeders\CampusSharedDemoSeeder;
use Illuminate\Support\Facades\DB;

class SharedDemoTest extends CampusTestCase
{
    public function test_snapshot_is_repeatable_and_keeps_other_accounts(): void
    {
        $other   = $this->student('real@example.test');
        $this->seed(CampusSharedDemoSeeder::class);
        $first   = DB::table('campus_students')->orderBy('id')->get()->toJson();
        $this->seed(CampusSharedDemoSeeder::class);
        $this->assertSame($first, DB::table('campus_students')->orderBy('id')->get()->toJson());
        $this->assertDatabaseCount('campus_students', 151);
        $this->assertDatabaseCount('campus_reviews', 230);
        $this->assertDatabaseHas('campus_students', ['id' => $other, 'email' => 'real@example.test']);
        $extra   = DB::table('campus_students')->where('email', 'like', 'campus-demo-extra-review-%@example.invalid');
        $this->assertSame(85, (clone $extra)->where('circle', '!=', '')->count());
        $seniors = DB::table('campus_students')->where('email', 'like', 'campus-senior-demo-%@example.invalid')->pluck('id');
        $this->assertSame(80, DB::table('campus_reviews')->whereIn('student_id', $seniors)->where('has_past_exam', 1)->count());
    }
}

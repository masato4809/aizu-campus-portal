<?php

declare(strict_types=1);

namespace Tests\Feature\Campus;

use Illuminate\Support\Facades\DB;

class ProfileInventoryTest extends CampusTestCase
{
    public function test_profile_counts_own_past_exam_courses_and_shows_club_membership(): void
    {
        $student = $this->student('inventory@example.test');
        $other   = $this->student('other@example.test');

        foreach ([['所持授業', $student, 1], ['未所持授業', $student, 0], ['他人の所持授業', $other, 1]] as [$name, $author, $hasExam]) {
            $course = DB::table('campus_courses')->insertGetId([
                'university' => '会津大学',
                'name'       => $name,
                'professor'  => '先生',
            ]);

            DB::table('campus_reviews')->insert([
                'student_id'    => $author,
                'course_id'     => $course,
                'has_past_exam' => $hasExam,
                'body'          => '口コミ',
            ]);
        }

        $this->withSession(['campus_student_id' => $student])->get('/campus/profile')
            ->assertOk()
            ->assertSee('過去問の取得数：1授業')
            ->assertSee('所持授業')
            ->assertDontSee('未所持授業')
            ->assertDontSee('他人の所持授業')
            ->assertSee('CUO')
            ->assertSee('サークルページへ');

        DB::table('campus_students')->where('id', $student)->update(['circle' => '']);
        DB::table('campus_reviews')->where('student_id', $student)->update(['has_past_exam' => 0]);

        $this->get('/campus/profile')
            ->assertOk()
            ->assertSee('過去問の取得数：0授業')
            ->assertSee('サークル未所属');
    }
}

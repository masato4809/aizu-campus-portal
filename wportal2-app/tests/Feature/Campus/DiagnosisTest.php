<?php

declare(strict_types=1);

namespace Tests\Feature\Campus;

class DiagnosisTest extends CampusTestCase
{
    public function test_diagnosis_page_renders(): void
    {
        $studentId = $this->student('viewer@example.test');

        $this->withSession(['campus_student_id' => $studentId])
            ->get('/campus/diagnosis')
            ->assertOk()
            ->assertSee('休日の過ごし方は？');
    }

    public function test_submitting_the_quiz_saves_personality_and_learning_type(): void
    {
        $studentId = $this->student('student@example.test');
        $answers   = array_fill(1, 9, 'a') + ['goal_orientation' => 'high_grade'];
        $formData  = [];
        foreach ($answers as $key => $value) {
            $formData[is_int($key) ? 'q'.$key : $key] = $value;
        }

        $this->withSession(['campus_student_id' => $studentId])
            ->post('/campus/diagnosis', $formData)
            ->assertRedirect('/campus/profile');

        $this->assertDatabaseHas('campus_students', ['id' => $studentId, 'personality' => 'AAA', 'goal_orientation' => 'high_grade']);

        $this->withSession(['campus_student_id' => $studentId])
            ->get('/campus/profile')
            ->assertOk()
            ->assertSee('太陽タイプ')
            ->assertSee('成績');
    }

    public function test_incomplete_answers_are_rejected(): void
    {
        $studentId = $this->student('incomplete@example.test');

        $this->withSession(['campus_student_id' => $studentId])
            ->post('/campus/diagnosis', ['q1' => 'a', 'goal_orientation' => 'high_grade'])
            ->assertSessionHasErrors('q2');
    }
}

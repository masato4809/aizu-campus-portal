<?php

declare(strict_types=1);

namespace Tests\Feature\Campus;

use Illuminate\Support\Facades\DB;

class RequiredDiagnosisTest extends CampusTestCase
{
    public function test_registration_requires_complete_diagnosis_before_using_campus(): void
    {
        $this->post('/campus/register', [
            'name'    => '診断テスト', 'email' => 'diagnosis-required@u-aizu.ac.jp', 'password' => 'password1234', 'password_confirmation' => 'password1234',
            'faculty' => 'コンピュータ理工学部', 'year' => 1, 'circle' => '', 'personality' => 'AAA',
        ])->assertSessionHasNoErrors()->assertRedirect('/campus/diagnosis');
        $student = DB::table('campus_students')->where('email', 'diagnosis-required@u-aizu.ac.jp')->first();
        $this->assertNotNull($student);
        $this->assertNull($student->personality);
        $this->get('/campus/diagnosis')->assertOk();
        foreach (['/campus', '/campus/profile', '/campus/matches', '/campus/courses/1'] as $url) {
            $this->get($url)->assertRedirect('/campus/diagnosis');
        }
        $this->post('/campus/review', [])->assertRedirect('/campus/diagnosis');
        $this->post('/campus/diagnosis', ['q1' => 'a'])->assertSessionHasErrors(['q2', 'goal_orientation']);
        $answers = ['goal_orientation' => 'high_grade'];
        for ($i = 1; $i <= 9; $i++) {
            $answers['q'.$i] = 'a';
        }
        $this->post('/campus/diagnosis', $answers)->assertSessionHasNoErrors()->assertRedirect('/campus/profile');
        $this->get('/campus')->assertOk();
        $this->post('/campus/profile', ['name' => '診断テスト', 'faculty' => 'コンピュータ理工学部', 'year' => 1, 'circle' => '', 'personality' => ''])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('campus_students', ['id' => $student->id, 'personality' => 'AAA']);
        DB::table('campus_students')->where('id', $student->id)->update(['goal_orientation' => null]);
        $this->post('/campus/logout')->assertRedirect('/campus/login');
        $this->post('/campus/login', ['email' => $student->email, 'password' => 'password1234'])->assertRedirect('/campus/diagnosis');
    }
}

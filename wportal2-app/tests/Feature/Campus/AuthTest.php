<?php

declare(strict_types=1);

namespace Tests\Feature\Campus;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthTest extends CampusTestCase
{
    public function test_registration_rejects_other_domains_and_accepts_uppercase_university_domain(): void
    {
        $data = ['name' => '学生', 'password' => 'password1234', 'password_confirmation' => 'password1234', 'faculty' => 'コンピュータ理工学部', 'year' => 1];
        foreach (['student@example.com', 'student@sub.u-aizu.ac.jp', 'student@u-aizu.ac.jp.example.com', 'student@fake-u-aizu.ac.jp'] as $email) {
            $this->post('/campus/register', $data + ['email' => $email])->assertSessionHasErrors('email');
            $this->assertDatabaseMissing('campus_students', ['email' => $email]);
        }
        $this->post('/campus/register', $data + ['email' => 'Student@U-AIZU.AC.JP'])->assertSessionHasNoErrors()->assertRedirect('/campus/diagnosis');
        $this->assertDatabaseHas('campus_students', ['email' => 'student@u-aizu.ac.jp']);
    }

    public function test_registration_login_and_private_pages(): void
    {
        $this->get('/campus')->assertRedirect('/campus/login');
        $this->get('/campus/register')->assertOk()->assertSee('学生情報の登録');
        $this->post('/campus/register', ['name' => '学生', 'email' => 'student@u-aizu.ac.jp', 'password' => 'password1234', 'password_confirmation' => 'password1234', 'faculty' => '理工学域', 'year' => 1])->assertRedirect('/campus/diagnosis');
        $student = DB::table('campus_students')->first();
        $this->assertNotNull($student);
        $this->assertSame('会津大学', $student->university);
        $this->assertTrue(Hash::check('password1234', $student->password));
        $answers = ['goal_orientation' => 'high_grade'];
        for ($i = 1; $i <= 9; $i++) {
            $answers['q'.$i] = 'a';
        }
        $this->post('/campus/diagnosis', $answers)->assertRedirect('/campus/profile');
        $this->get('/campus')->assertOk();
        $this->post('/campus/logout')->assertRedirect('/campus/login');
        $this->get('/campus')->assertRedirect('/campus/login');
        $this->post('/campus/login', ['email' => $student->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/campus/login', ['email' => $student->email, 'password' => 'password1234'])->assertRedirect('/campus');
    }
}

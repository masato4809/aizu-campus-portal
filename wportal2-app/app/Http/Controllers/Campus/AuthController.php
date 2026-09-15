<?php

declare(strict_types=1);

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $data    = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        $student = DB::table('campus_students')->where('email', mb_strtolower($data['email']))->first();
        if (! $student || ! Hash::check($data['password'], $student->password)) {
            throw ValidationException::withMessages(['email' => 'メールアドレスまたはパスワードが違います。']);
        }
        $request->session()->regenerate();
        $request->session()->put('campus_student_id', $student->id);

        return redirect(\App\Http\Middleware\RequireCampusDiagnosis::complete($student) ? '/campus' : '/campus/diagnosis');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/campus/login');
    }
}

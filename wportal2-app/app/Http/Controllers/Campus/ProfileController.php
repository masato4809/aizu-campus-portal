<?php

declare(strict_types=1);

namespace App\Http\Controllers\Campus;

use App\Campus\CampusOptions;
use App\Campus\CurrentStudent;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function save(Request $request, string $action): RedirectResponse
    {
        $me                 = app(CurrentStudent::class)->get($request);
        $request->merge(['university' => CampusOptions::UNIVERSITY]);
        $rules              = [
            'name'        => 'required|string|max:80',
            'university'  => 'required|string|max:120',
            'faculty'     => 'required|string|max:120',
            'year'        => 'required|integer|min:1|max:6',
            'circle'      => [
                'nullable',
                'string',
                'max:120',
                Rule::exists('campus_club_catalog', 'name')->where(fn ($query) => $query->where('university', CampusOptions::UNIVERSITY)),
            ],
            'personality' => ['nullable', Rule::in(CampusOptions::TYPES)],
        ];
        if ($action === 'register') {
            $request->merge(['email' => mb_strtolower((string) $request->input('email'))]);
            $rules += ['email' => 'required|email|max:255|ends_with:@u-aizu.ac.jp|unique:campus_students,email', 'password' => 'required|string|min:10|max:128|confirmed'];
        }
        $data               = $request->validate($rules, [
            'email.ends_with' => '会津大学のメールアドレス（@u-aizu.ac.jp）で登録してください。',
        ]);
        unset($data['personality']); // Only the complete diagnosis may set or change the type.
        $data['circle']     = $data['circle'] ?? '';
        $data['updated_at'] = now();
        if ($action === 'register') {
            $data['password']   = Hash::make($data['password']);
            $data['created_at'] = now();
            $id                 = DB::table('campus_students')->insertGetId($data);
            $request->session()->regenerate();
            $request->session()->put('campus_student_id', $id);
        } else {
            $me = app(CurrentStudent::class)->requireStudent($request);
            // University identifies the community and cannot be changed after registration.
            unset($data['university']);
            DB::table('campus_students')->where('id', $me->id)->update($data);
        }

        return redirect($action === 'register' ? '/campus/diagnosis' : '/campus')->with('status', $action === 'register' ? '続けて性格・学び方診断に回答してください。' : 'プロフィールを保存しました。');
    }
}

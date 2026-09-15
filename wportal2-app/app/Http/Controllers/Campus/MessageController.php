<?php

declare(strict_types=1);

namespace App\Http\Controllers\Campus;

use App\Campus\CurrentStudent;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $me   = app(CurrentStudent::class)->requireStudent($request);
        $data = $request->validate(['recipient_id' => ['required', 'integer', Rule::exists('campus_students', 'id')->where('university', $me->university), Rule::notIn([$me->id])], 'body' => 'required|string|max:2000']);
        DB::table('campus_messages')->insert($data + ['sender_id' => $me->id, 'created_at' => now(), 'updated_at' => now()]);

        return redirect('/campus/messages?peer='.$data['recipient_id'])->with('status', 'メッセージを送信しました。');
    }
}

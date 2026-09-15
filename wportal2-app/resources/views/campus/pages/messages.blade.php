@extends('campus.layout')
@section('content')
@include('campus.components.page-intro')

<div class="message-layout">
<section class="card">
<h2>メンバー</h2>@forelse($contacts as $contact)<a class="contact {{ $peer?->id === $contact->id ? 'selected' : '' }}" href="/campus/messages?peer={{ $contact->id }}">{{ $contact->name }} <span class="muted">{{ $contact->year }}年</span>
</a>@empty<p class="muted">他のメンバーはまだいません。</p>@endforelse</section>
<section class="card">@if($peer)<h2>{{ $peer->name }}さんとのメッセージ</h2>
<p class="muted small">この会話はあなたと相手だけが閲覧できます。新着はページを再読み込みして確認してください。</p>
<div class="messages">@forelse($messages as $message)<div class="bubble {{ $message->sender_id === $me->id ? 'mine' : '' }}">
<p class="preserve">{{ $message->body }}</p>
<small>{{ $message->created_at }}</small>
</div>@empty<p class="empty">はじめてのメッセージを送ってみましょう。</p>@endforelse</div>
<form method="post" action="/campus/message">@csrf<input type="hidden" name="recipient_id" value="{{ $peer->id }}">
<label>メッセージ<textarea name="body" required maxlength="2000" placeholder="こんにちは。授業について相談したいです。">{{ old('body') }}</textarea>
</label>
<button class="button">送信する</button>
</form>@else<div class="empty">メンバーを選ぶと会話を開けます。</div>@endif
</section>
</div>
@endsection

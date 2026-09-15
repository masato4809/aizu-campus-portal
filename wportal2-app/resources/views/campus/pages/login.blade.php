@extends('campus.layout')
@section('content')
<section class="login-page" aria-labelledby="login-title">
<div class="login-layout">
<div class="login-identity">
<div class="login-identity__topline">
<span class="login-caption">AIZU UNIVERSITY / STUDENT COMMUNITY</span>
<span class="campus-mark" aria-hidden="true"><span></span></span>
</div>
<h1 id="login-title">おかえりなさい。</h1>
<p class="login-lede">授業の口コミも、先輩との出会いも。ここから広がる大学生活。</p>

<div class="login-tiles" aria-label="Campus Linkでできること">
<article class="login-tile login-tile--cyan">
<span class="login-tile__index">01</span>
<h2>授業の口コミを読む</h2>
<p>先輩のリアルな声を、次の授業選びに。</p>
</article>
<article class="login-tile login-tile--coral">
<span class="login-tile__index">02</span>
<h2>気になることを話す</h2>
<p>履修や大学生活の相談を、ひとことから。</p>
</article>
</div>
</div>

<div class="login-panel">
<div class="login-panel__intro">
<span class="login-panel__label">CAMPUS LINK</span>
<h2>ログイン</h2>
<p>登録したメールアドレスで続けます。</p>
</div>

<form class="login-form" method="post" action="/campus/login">
@csrf
<div class="login-field">
<label for="login-email">メールアドレス <span aria-hidden="true">*</span></label>
<input id="login-email" type="email" name="email" value="{{ old('email') }}" required aria-required="true" autocomplete="email" maxlength="255" @if($errors->has('email')) aria-invalid="true" aria-describedby="login-email-error" @else aria-describedby="login-email-note" @endif>
@error('email')<p class="field-error" id="login-email-error">{{ $message }}</p>@else<p class="field-note" id="login-email-note">登録時に使ったメールアドレス</p>@enderror
</div>
<div class="login-field">
<label for="login-password">パスワード <span aria-hidden="true">*</span></label>
<input id="login-password" type="password" name="password" required aria-required="true" autocomplete="current-password" maxlength="128" @if($errors->has('password')) aria-invalid="true" aria-describedby="login-password-error" @else aria-describedby="login-password-note" @endif>
@error('password')<p class="field-error" id="login-password-error">{{ $message }}</p>@else<p class="field-note" id="login-password-note">登録時に設定したパスワード</p>@enderror
</div>
<button class="button login-submit" type="submit">
<span class="login-submit__label">ログインする</span>
<span class="login-submit__state" aria-hidden="true"></span>
</button>
<p class="login-form__note">Campus Link は会津大学の学生コミュニティです。</p>
</form>

<p class="login-register">はじめての方は <a href="/campus/register">新規登録へ <span aria-hidden="true">→</span></a></p>
</div>
</div>
</section>
<script>
document.querySelector('.login-form')?.addEventListener('submit', (event) => {
    const submit = event.currentTarget.querySelector('.login-submit');
    if (!submit) return;
    submit.dataset.state = 'loading';
    submit.setAttribute('aria-disabled', 'true');
});
</script>
@endsection

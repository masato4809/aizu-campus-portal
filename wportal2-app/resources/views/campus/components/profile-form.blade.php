<section class="intro">
<p class="eyebrow">YOUR CAMPUS STARTS HERE</p>
<h1>{{ $page === 'login' ? 'おかえりなさい。' : ($page === 'register' ? '大学生活に、新しいつながりを。' : 'あなたのプロフィール') }}</h1>
<p>授業の口コミも、先輩との出会いも。ここから広がる大学生活。</p>
</section>
<div class="card form-card">
<h2>{{ $page === 'login' ? 'ログイン' : '学生情報の登録' }}</h2>
<form method="post" action="/campus/{{ $page }}">@csrf
@if($page !== 'login')
<label>表示名<input name="name" maxlength="80" value="{{ old('name', $me->name ?? '') }}" required autocomplete="nickname">
</label>
<input type="hidden" name="university" value="会津大学">
<p>大学：会津大学</p>
<div class="form-grid">
<label>学域・学類<input name="faculty" maxlength="120" value="{{ old('faculty', $me->faculty ?? '') }}" placeholder="例：理工学域 電子情報通信学類" required>
</label>
<label>学年<select name="year">@for($i=1;$i<=6;$i++)<option value="{{ $i }}" @selected(old('year', $me->year ?? 1)==$i)>{{ $i }}年</option>@endfor</select>
</label>
</div>
<label>所属サークル（任意）
<select name="circle">
<option value="">所属していなければ未選択</option>
@foreach($clubOptions as $clubOption)
<option value="{{ $clubOption->name }}" @selected(old('circle', $me->circle ?? '') === $clubOption->name)>{{ $clubOption->name }}（{{ $clubOption->category }}）</option>
@endforeach
</select>
</label>
<p class="muted small">会津大学公式の掲載団体から選択してください。現在の所属サークルに、あなたの口コミ投稿数と診断結果が集計されます。</p>
@if($page === 'profile')<div class="card" style="margin-top:1rem">
<h3>性格・学び方診断</h3>
@if($me->personality ?? null)<p>性格タイプ：{{ $typeLabels[$me->personality] ?? $me->personality }}</p>
<p>目標：{{ $goalOrientations[$me->goal_orientation] ?? '未診断' }}</p>@else
<p class="muted small">まだ診断していません。サークルの相性や、目的に合った授業を見つけやすくなります。</p>@endif
<a class="button" href="/campus/diagnosis">診断{{ ($me->personality ?? null) ? 'をやり直す' : 'する' }} →</a>
</div>@endif
@endif

@if($page !== 'profile')
<label>メールアドレス<input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" maxlength="255">
</label>
@if($page === 'register')<p class="muted small">会津大学のメールアドレス（@u-aizu.ac.jp）のみ登録できます。</p>@endif
<label>パスワード<input type="password" name="password" required autocomplete="{{ $page === 'register' ? 'new-password' : 'current-password' }}" @if($page==='register') minlength="10" @endif
 maxlength="128">
</label>
@if($page === 'register')<label>パスワード（確認）<input type="password" name="password_confirmation" minlength="10" maxlength="128" required autocomplete="new-password">
</label>
<p class="muted small">10文字以上。表示名・学類・学年・サークル・性格タイプは学内ユーザーに表示されます。メールアドレスは公開されません。</p>@endif

@endif

<button class="button">{{ $page === 'login' ? 'ログインする' : '保存してはじめる' }}</button>
</form>
@if($page === 'login')<p>はじめての方は <a href="/campus/register">新規登録へ →</a>
</p>@elseif($page === 'register')<p>登録済みの方は <a href="/campus/login">ログインへ →</a>
</p>@endif

</div>

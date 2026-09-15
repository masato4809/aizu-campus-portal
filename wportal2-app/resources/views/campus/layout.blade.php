<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Campus Link | 会津大学の授業とつながり</title>
@if($page === 'login')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
@endif
<link rel="stylesheet" href="/campus.css">
</head>
<body class="campus-page campus-page-{{ $page }}">
@if($page !== 'login')
@include('campus.components.sidebar')
@endif

<main class="{{ $page === 'login' ? 'main-auth' : '' }}">
@if($page === 'login')
<header class="login-nav">
<a class="login-nav__brand" href="/campus/login" aria-label="Campus Link ホーム">
<span class="brand-icon">C</span><span>Campus Link</span>
</a>
<a class="login-nav__link" href="/campus/register">新規登録 <span aria-hidden="true">→</span></a>
</header>
@else
<header>
<span>会津大学 <span class="muted">/ 学生コミュニティ</span>
</span>
<span class="pill">CAMPUS LIFE, CONNECTED</span>
</header>
@endif
@if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif

@if($errors->any())<div class="error" role="alert">
<strong>入力内容を確認してください</strong>
<ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
</ul>
</div>@endif

@yield('content')
@if($page === 'login')
<footer class="login-footer">
<p>Campus Link <span>会津大学の学生生活を、もっと自分らしく。</span></p>
</footer>
@else
<footer>Campus Link <span>会津大学の学生生活を、もっと自分らしく。</span>
</footer>
@endif
</main>
</body>
</html>

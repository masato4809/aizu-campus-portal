@extends('campus.layout')
@section('content')
<section class="card">
<h1>教授を探す</h1>
<p class="muted">会津大学の先生の担当授業・学生の声を確認できます。</p>
<form method="get" action="/campus/professors">
<label>名前・研究分野<input name="q" value="{{ request('q') }}" maxlength="120" placeholder="日本語・英語で検索"></label>
<button class="button">検索する</button>
</form>
<p>{{ $teachers->total() }}人の先生</p>
</section>
<div class="course-grid">
@forelse($teachers as $teacher)
<article class="card">
<h2><a href="/campus/professors/{{ $teacher->id }}">{{ $teacher->name }}</a></h2>
<p class="muted">{{ $teacher->name_en }}</p>
<p>{{ $teacher->department }} {{ $teacher->position }}</p>
<a href="/campus/professors/{{ $teacher->id }}">担当授業・評価を見る →</a>
</article>
@empty
<p class="empty">該当する先生がいません。検索語を変えてください。</p>
@endforelse
</div>
<nav aria-label="教授一覧のページ">@if($teachers->previousPageUrl())<a href="{{ $teachers->previousPageUrl() }}">← 前へ</a>@endif
<span>{{ $teachers->currentPage() }} / {{ $teachers->lastPage() }}</span>
@if($teachers->nextPageUrl())<a href="{{ $teachers->nextPageUrl() }}">次へ →</a>@endif</nav>
@endsection

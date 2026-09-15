@extends('campus.layout')
@section('content')
<p><a href="/campus/professors">← 教授を探す</a></p>
<section class="card">
<h1>{{ $teacher->name }}</h1>
<p class="muted">{{ $teacher->name_en }}</p>
<p>{{ $teacher->department }} {{ $teacher->position }}</p>
<p>{{ $teacher->research_field }}</p>
@if($teacher->profile_url && preg_match('/^https?:\/\//i', $teacher->profile_url))<a href="{{ $teacher->profile_url }}" target="_blank" rel="noopener noreferrer">大学公式プロフィール ↗</a>@endif
</section>
<section class="card">
<h2>先生評価</h2>
<p><strong>{{ $teacher_rating_average === null ? '未評価' : number_format($teacher_rating_average, 2).' / 5' }}</strong> · {{ $teacher_review_count }}件</p>
<h2>みんなの授業評価の平均</h2>
<p class="muted">担当授業に寄せられた全{{ $course_review_count }}件の口コミを、項目ごとにまとめて集計しています。</p>
@include('campus.components.teacher-radar-chart')
</section>
<section class="card">
<h2>教えている授業</h2>
@forelse($courses as $course)
<p><a href="/campus/courses/{{ $course->id }}">{{ $course->course_code }} {{ $course->name }} →</a><br><span class="muted">{{ $course->academic_year }}年度 · {{ $course->semester }}</span></p>
@empty<p class="muted">紐付いた担当授業はまだありません。</p>@endforelse
</section>
<section class="card">
<h2>先生はどんな人？</h2>
@forelse($comments as $comment)
<article><h3><a href="/campus/students/{{ $comment->author_id }}">{{ $comment->author }} →</a> · {{ $comment->rating }} / 5</h3><p style="white-space: pre-wrap">{{ $comment->body }}</p><p class="muted">{{ $comment->updated_at }}</p></article>
@empty<p class="muted">まだコメントがありません。</p>@endforelse
<nav aria-label="コメントのページ">@if($comments->previousPageUrl())<a href="{{ $comments->previousPageUrl() }}">← 前へ</a>@endif @if($comments->nextPageUrl())<a href="{{ $comments->nextPageUrl() }}">次へ →</a>@endif</nav>
</section>
<section class="card">
<h2>先生へのコメント・評価を投稿</h2>
<p class="muted">同じ先生への再投稿は、あなたの投稿を更新します。</p>
<form method="post" action="/campus/teachers/{{ $teacher->id }}/reviews">
@csrf
<label>先生評価（1〜5）<select name="rating" required><option value="">選択してください</option>@for($score=1;$score<=5;$score++)<option value="{{ $score }}" @selected(old('rating', $myReview->rating ?? '') == $score)>{{ $score }}</option>@endfor</select></label>
<label>コメント（500字以内）<textarea name="body" maxlength="500" required>{{ old('body', $myReview->body ?? '') }}</textarea></label>
<button class="button">{{ $myReview ? '投稿を更新する' : '投稿する' }}</button>
</form>
</section>
@endsection

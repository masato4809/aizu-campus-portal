@extends('campus.layout')
@section('content')
@include('campus.components.page-intro')

<p class="notice">一致度は共通点の目安です。性格の3軸60点・学習目標20点・学部15点・サークル5点。未登録項目は加点しません。性格の相性を診断するものではありません。</p>
<div class="course-grid">
@forelse($matches as $senior)
@php($seniorReviews = $reviewsByStudent->get($senior->id, collect()))
<article class="card">
<div class="section-heading">
<div class="avatar">{{ mb_substr($senior->name,0,1) }}</div>
<span class="match-score {{ $senior->score >= 70 ? 'tier-high' : ($senior->score >= 40 ? 'tier-mid' : 'tier-low') }}">{{ $senior->score }}<small> / 100</small>
</span>
</div>
<h2>{{ $senior->name }}</h2>
<p>{{ $senior->faculty }} · {{ $senior->year }}年</p>
<dl class="senior-profile-facts">
<div><dt>所属サークル</dt><dd>@if($senior->circle)<a href="{{ url('/campus/clubs').'?'.http_build_query(['circle' => $senior->circle]) }}">{{ $senior->circle }} →</a>@else サークル未所属 @endif</dd></div>
<div><dt>性格タイプ</dt><dd>{{ \App\Campus\CampusOptions::TYPE_LABELS[$senior->personality ?? ''] ?? ($senior->personality ?: '未登録') }}</dd></div>
</dl>
<div class="tags">@foreach(array_filter($senior->reasons, fn ($reason) => $reason !== '同じ大学') as $reason)<span class="pill">{{ $reason }}</span>@endforeach
</div>
<div class="tags" aria-label="投稿数と過去問所持数">
    <span class="pill">投稿 {{ $seniorReviews->count() }}件</span>
    <span class="pill {{ (int) $senior->past_exam_count > 0 ? 'past-exams-available' : 'past-exams-unavailable' }}">{{ (int) $senior->past_exam_count > 0 ? '過去問 '.(int) $senior->past_exam_count.'問所持' : '過去問なし' }}</span>
</div>
<div class="senior-past-exams">
<h3>過去問を持っている授業</h3>
@forelse($seniorReviews->where('has_past_exam', 1)->unique('course_id') as $examReview)
<p><a href="/campus/courses/{{ $examReview->course_id }}">{{ $examReview->course_name }} →</a></p>
@empty<p class="muted">過去問ありの授業投稿はありません。</p>@endforelse
</div>
<details>
<summary>先輩の授業投稿（{{ $seniorReviews->count() }}）</summary>
@forelse($seniorReviews as $review)
<div class="review">
<a href="/campus/courses/{{ $review->course_id }}">{{ $review->course_name }}</a>
@include('campus.components.review-detail', ['review' => $review])
</div>
@empty<p>まだ投稿はありません。</p>@endforelse
</details>
</article>@empty<div class="empty">登録された上級生はまだいません。プロフィールを整えて、先輩の参加を待ちましょう。</div>@endforelse</div>

@endsection

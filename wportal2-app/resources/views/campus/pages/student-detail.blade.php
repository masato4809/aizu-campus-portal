@extends('campus.layout')
@section('content')
<section class="card">
<h1>{{ $profile->name }}</h1>
<p>{{ $profile->faculty }} · {{ $profile->year }}年</p>
<p>所属サークル：@if($profile->circle)<a href="{{ url('/campus/clubs').'?'.http_build_query(['circle' => $profile->circle]) }}">{{ $profile->circle }} →</a>@else 未所属 @endif</p>
<p>性格タイプ：{{ \App\Campus\CampusOptions::TYPE_LABELS[$profile->personality ?? ''] ?? '未登録' }}</p>
<p>目標：{{ \App\Campus\CampusOptions::GOAL_ORIENTATIONS[$profile->goal_orientation ?? ''] ?? '未登録' }}</p>
<div class="tags"><span class="pill">投稿 {{ $reviews->count() }}件</span><span class="pill">{{ $profile->past_exam_count ? '過去問 '.$profile->past_exam_count.'問所持' : '過去問なし' }}</span></div>
<h2>過去問を持っている授業</h2>
@forelse($reviews->where('has_past_exam', 1)->unique('course_id') as $review)
<p><a href="/campus/courses/{{ $review->course_id }}">{{ $review->course_name }} →</a></p>
@empty<p>過去問ありの授業投稿はありません。</p>@endforelse
</section>
<section class="card"><h2>この人の授業投稿</h2>
@forelse($reviews as $review)
<h3><a href="/campus/courses/{{ $review->course_id }}">{{ $review->course_name }}</a></h3>
@include('campus.components.review-detail')
@empty<p>まだ授業投稿はありません。</p>@endforelse
</section>
@endsection

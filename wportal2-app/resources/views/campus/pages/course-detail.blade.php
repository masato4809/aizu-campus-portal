@extends('campus.layout')
@section('content')
<p><a href="/campus/courses">← 授業一覧</a></p>
<p class="muted">{{ $course->course_code }} · {{ $course->academic_year }}年度 · {{ $course->semester }}</p>
<section class="card" aria-labelledby="review-filter-title">
<h2 id="review-filter-title">口コミを絞り込む</h2>
<form method="get" action="/campus/courses/{{ $course->id }}" class="toolbar">
<label>投稿者の属性
<select name="user_type" onchange="this.form.submit()" aria-label="口コミの投稿者属性で絞り込む">
<option value="">すべて表示</option>
@foreach($userTypes as $key => $label)<option value="{{ $key }}" @selected($selectedUserType === $key)>{{ $label }}</option>@endforeach
</select>
</label>
</form>
@if($selectedUserType)<p class="muted small">{{ $userTypes[$selectedUserType] }}の口コミを表示中（{{ $course->review_count }}件）</p>@endif
</section>
@include('campus.components.course-card')
@if($course->description)<section class="card"><h2>授業概要</h2><p style="white-space: pre-wrap">{{ $course->description }}</p></section>@endif
@if($course->syllabus_url && preg_match('/^https?:\/\//i', $course->syllabus_url))<p><a href="{{ $course->syllabus_url }}" target="_blank" rel="noopener noreferrer">公式シラバスを見る ↗</a></p>@endif
@endsection

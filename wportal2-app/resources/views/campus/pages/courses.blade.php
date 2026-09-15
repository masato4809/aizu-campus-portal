@extends('campus.layout')
@section('content')
<h1>授業を探す</h1>
@include('campus.components.course-search')
@if($hasSearch)
<div class="course-grid">
@forelse($courses as $course)
@include('campus.components.course-card', ['course' => $course])
@empty
<p class="empty">該当する授業がありません。検索語を変えてください。</p>
@endforelse
</div>
@endif
@include('campus.components.review-form')
@endsection

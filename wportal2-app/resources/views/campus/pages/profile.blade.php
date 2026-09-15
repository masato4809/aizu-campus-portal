@extends('campus.layout')
@section('content')
<section class="card">
<h1>プロフィール</h1>
<h2>過去問の取得数：{{ $pastExamCourses->count() }}授業</h2>
<p class="muted">自分の口コミで「過去問あり」と登録した授業数です。</p>
<h3>過去問を持っている授業</h3>
@forelse($pastExamCourses as $course)
<p><a href="/campus/courses/{{ $course->id }}">{{ $course->course_code }} {{ $course->name }} →</a>@if($course->academic_year)<span class="muted">（{{ $course->academic_year }}年度）</span>@endif</p>
@empty
<p class="muted">「過去問あり」で登録した授業はまだありません。</p>
@endforelse
<h2>所属サークル</h2>
@if(trim($me->circle) !== '')
<p>{{ $me->circle }}</p>
<a href="{{ url('/campus/clubs') }}">サークルページへ →</a>
@else
<p class="muted">サークル未所属</p>
@endif
</section>
@include('campus.components.profile-form')
@endsection

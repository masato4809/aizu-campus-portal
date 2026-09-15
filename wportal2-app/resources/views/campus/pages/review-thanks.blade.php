@extends('campus.layout')
@section('content')
<section class="card">
<span class="pill">投稿完了</span>
<h1>ありがとうございました</h1>
<p>授業の口コミを保存しました。授業選びの参考として、みんなに共有されます。</p>
<p><a class="button" href="/campus/courses/{{ $courseId }}">投稿した授業を見る →</a></p>
<p><a class="button secondary" href="/campus/courses">授業を探す</a></p>
</section>
@endsection

<article class="card">
<div class="section-heading">
<span class="pill">会津大学</span>
<span class="muted small">{{ $course->review_count }}件の口コミ</span>
</div>
<h2><a href="/campus/courses/{{ $course->id }}">{{ $course->name }}</a></h2>
@forelse($course->teachers->unique('id') as $teacher)
<p><a href="/campus/professors/{{ $teacher->id }}">{{ $teacher->name }}{{ $teacher->name_en ? ' / '.$teacher->name_en : '' }} →</a></p>
@empty
<p class="muted">{{ $course->professor ?: '担当教員は未登録です' }}</p>
@endforelse
@include('campus.components.radar-chart', ['course' => $course, 'ratings' => $ratings])

<details @if(request()->is('campus/courses/*')) open @endif>
<summary>口コミを読む（{{ $course->review_count }}）</summary>
@foreach($course->reviews as $review)
@include('campus.components.review-detail', ['review' => $review])
@endforeach

</details>
</article>

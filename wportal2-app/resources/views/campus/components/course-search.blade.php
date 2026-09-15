<div class="toolbar">
<form class="search" method="get" action="/campus/courses">
<input list="course-search-suggestions" autocomplete="off" aria-label="授業名・教授名・科目コードで検索" name="q" value="{{ request('q') }}" placeholder="授業名・教授名・科目コードで検索" maxlength="120">
<datalist id="course-search-suggestions">
@foreach($courseOptions->unique('name') as $option)
<option value="{{ $option->name }}">{{ $option->course_code }} / {{ $option->name_en }} / {{ $option->professor }}</option>
@endforeach
</datalist>
<button class="button">検索</button>
</form>
<a class="button secondary" href="#post-review">＋ 授業の評価を投稿</a>
</div>
@if($hasSearch)
<div class="section-heading">
<h2>検索結果</h2>
<span class="muted">{{ $courses->count() }}件の授業</span>
</div>
@endif

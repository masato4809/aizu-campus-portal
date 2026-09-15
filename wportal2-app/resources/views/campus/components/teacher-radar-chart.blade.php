<div class="rating-layout">
<svg class="radar" viewBox="0 0 240 220" role="img" aria-label="{{ $teacher->name }}の担当授業の5項目平均評価。未評価の項目は点を表示しません。各数値は横の一覧に表示">
    @for($level = 1; $level <= 5; $level++)
        <polygon points="@for($i = 0; $i < 5; $i++){{ 120 + 78 * $level / 5 * cos(deg2rad(-90 + $i * 72)) }},{{ 108 + 78 * $level / 5 * sin(deg2rad(-90 + $i * 72)) }} @endfor" fill="none" stroke="#dfe7e5" />
    @endfor
    @if(count(array_filter($course_review_averages, fn ($value) => $value !== null)) === 5)
        <polygon points="@foreach(array_keys($ratings) as $i => $key){{ 120 + 78 * $course_review_averages[$key] / 5 * cos(deg2rad(-90 + $i * 72)) }},{{ 108 + 78 * $course_review_averages[$key] / 5 * sin(deg2rad(-90 + $i * 72)) }} @endforeach" fill="#23927b33" stroke="#23836e" stroke-width="2" />
    @endif
    @foreach($ratings as $key => $label)
        @php($i = array_search($key, array_keys($ratings), true))
        @if($course_review_averages[$key] !== null)
            <circle cx="{{ 120 + 78 * $course_review_averages[$key] / 5 * cos(deg2rad(-90 + $i * 72)) }}" cy="{{ 108 + 78 * $course_review_averages[$key] / 5 * sin(deg2rad(-90 + $i * 72)) }}" r="3" fill="#23836e" />
        @endif
        <text x="{{ 120 + 98 * cos(deg2rad(-90 + $i * 72)) }}" y="{{ 112 + 98 * sin(deg2rad(-90 + $i * 72)) }}" text-anchor="middle" font-size="12" fill="#536761">{{ $i + 1 }}</text>
    @endforeach
</svg>
<dl class="ratings">
    @foreach($ratings as $key => $label)
        <div><dt>{{ array_search($key, array_keys($ratings), true) + 1 }}. {{ $label }}</dt><dd>{{ $course_review_averages[$key] === null ? '未評価' : number_format($course_review_averages[$key], 2).' / 5' }}（{{ $course_review_rating_counts[$key] }}件）</dd></div>
    @endforeach
</dl>
</div>
@if(in_array(null, $course_review_averages, true))
<p class="muted small">未評価の項目は点を表示しません。5項目すべてに評価が集まると面で表示します。</p>
@endif

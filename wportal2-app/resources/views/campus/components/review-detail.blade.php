<div class="review" id="review-{{ $review->id }}">
    <strong><a href="/campus/students/{{ $review->student_id }}">{{ $review->author }}</a></strong>
    <span class="muted small"> · {{ $review->year }}年</span>
    <p class="review-author-circle">所属サークル：
        @if(filled($review->author_circle))
            <a href="{{ url('/campus/clubs').'?'.http_build_query(['circle' => $review->author_circle]) }}">{{ $review->author_circle }} →</a>
        @else
            サークル未所属
        @endif
    </p>
    <p class="review-author-personality">性格タイプ：{{ \App\Campus\CampusOptions::TYPE_LABELS[$review->author_personality ?? ''] ?? ($review->author_personality ?: '未登録') }}</p>
    <p class="review-author-type">投稿者の属性：{{ \App\Campus\CampusOptions::USER_TYPES[$review->user_type ?? ''] ?? '未登録' }}</p>
    @if(isset($review->match_score) && $review->match_score !== null)
        <p class="review-match-score">あなたとの一致度：<strong>{{ $review->match_score }} / 100点</strong></p>
    @endif
    <dl class="review-details">
        <div><dt>学部・学域</dt><dd>{{ $review->faculty ?? '未登録' }}</dd></div>
        <div><dt>学科・学類</dt><dd>{{ $review->department ?? '未登録' }}</dd></div>
        <div><dt>出席方法</dt><dd>{{ $review->attendance_method ?? '未登録' }}</dd></div>
        <div><dt>課題の量</dt><dd>{{ [1 => 'ほとんどない', 2 => '少ない', 3 => '普通', 4 => '多い', 5 => 'とても多い'][$review->assignment_amount] ?? '未登録' }}</dd></div>
        <div><dt>リモート割合</dt><dd>{{ $review->remote_percentage === null ? '未登録' : $review->remote_percentage.'%' }}</dd></div>
        <div><dt>教材</dt><dd class="preserve">{{ $review->materials ?? '未登録' }}</dd></div>
    </dl>
    <p class="preserve">{{ $review->body }}</p>
    <p>役に立った：{{ $review->helpful_count }}件</p>
    @if((int) $review->student_id !== (int) $me->id)
        <form method="post" action="/campus/reviews/{{ $review->id }}/helpful">
            @csrf
            <input type="hidden" name="helpful" value="{{ $review->helpful_by_me ? 0 : 1 }}">
            <button class="button" type="submit" aria-pressed="{{ $review->helpful_by_me ? 'true' : 'false' }}" aria-label="{{ $review->helpful_by_me ? '役に立ったを取り消す' : '役に立った' }}" title="{{ $review->helpful_by_me ? '役に立ったを取り消す' : '役に立った' }}"><span aria-hidden="true">👍</span> {{ $review->helpful_count }}</button>
        </form>
    @endif

</div>

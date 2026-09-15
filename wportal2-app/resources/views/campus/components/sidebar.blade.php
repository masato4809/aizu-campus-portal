<aside class="sidebar">
<a class="brand" href="/campus">
<span class="brand-icon">C</span> Campus Link</a>
<p class="eyebrow">AIZU UNIVERSITY</p>
@if($me)
<nav aria-label="メインナビゲーション">
@foreach(['courses'=>'授業を探す','professors'=>'教授を探す','matches'=>'先輩とつながる','clubs'=>'サークル','diagnosis'=>'性格・学び方診断','profile'=>'プロフィール'] as $key=>$label)
<a class="{{ $page === $key ? 'active' : '' }}" href="/campus/{{ $key }}">{{ $label }} <span>›</span>
</a>
@endforeach

</nav>
<div class="sidebar-bottom">
<strong>{{ $me->name }}</strong>
<p>{{ $me->faculty }} · {{ $me->year }}年</p>
<form method="post" action="/campus/logout">@csrf<button class="text-button">ログアウト</button>
</form>
</div>
@else
<p class="sidebar-copy">授業選びに、<br>先輩のリアルな声を。</p>
@endif

</aside>

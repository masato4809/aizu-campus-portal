<div class="club-post">
    <p class="preserve">{{ $post->body }}</p>
    @if($post->images->isNotEmpty())
        <div class="club-gallery">
            @foreach($post->images as $image)
                <a href="/campus/club-images/{{ $image->id }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $club->name }}の活動写真{{ $loop->iteration }}を開く">
                    <img src="/campus/club-images/{{ $image->id }}" alt="{{ $club->name }}の活動写真 {{ $loop->iteration }}" width="{{ $image->width }}" height="{{ $image->height }}" loading="lazy">
                </a>
            @endforeach
        </div>
    @endif
    <p class="muted small">投稿者：{{ $post->author }} · {{ substr($post->created_at, 0, 10) }}</p>
    <div class="club-links">
        @if($post->contact_url)<a class="button secondary" href="{{ $post->contact_url }}" target="_blank" rel="noopener noreferrer">SNSなどで連絡する →</a>@endif
        @if($post->website_url)<a class="button secondary" href="{{ $post->website_url }}" target="_blank" rel="noopener noreferrer">ホームページ →</a>@endif
    </div>
    @if($post->student_id === $me->id)
        <details class="club-delete">
            <summary>この投稿を削除</summary>
            <p class="small">活動・募集情報と添付画像を削除します。この操作は取り消せません。</p>
            <form method="post" action="/campus/club/{{ $post->id }}">
                @csrf
                @method('DELETE')
                <button class="button secondary" type="submit">投稿と画像を削除する</button>
            </form>
        </details>
    @endif
</div>

@extends('campus.layout')
@section('content')
@include('campus.components.page-intro')
@if(request()->filled('circle'))<section class="card"><h2>{{ request('circle') }}</h2><a href="/campus/clubs">すべてのサークルを見る →</a></section>@endif

<div class="club-heading">
    <p>口コミで大学生活を支えるサークルと、自分に似た仲間を見つけよう。</p>
    <a class="button" href="#club-post-form">＋ 活動・募集情報を投稿</a>
</div>
<nav class="club-tabs" aria-label="サークルランキングの切り替え">
    <a href="/campus/clubs" @if($clubTab === 'reviews') aria-current="page" @endif>口コミ投稿数ランキング</a>
    <a href="/campus/clubs?tab=similarity" @if($clubTab === 'similarity') aria-current="page" @endif>あなたに似ているサークル</a>
</nav>

@if($clubTab === 'similarity' && !$me->personality)
    <div class="empty">
        <h2>診断結果を登録すると、似ているサークルが見つかります</h2>
        <p>プロフィールに16タイプの診断結果を登録してください。</p>
        <a class="button" href="/campus/profile">診断結果を登録する →</a>
    </div>
@else
    <p class="muted small club-ranking-note">
        @if($clubTab === 'reviews')
            現在の所属メンバーによる授業・教授の口コミの累計です。同じ授業への更新は1件として数えます。
        @else
            あなたのタイプ：{{ $me->personality }}。診断結果の登録者に占める、同じタイプの人の割合順です。性格の相性を保証するものではありません。
        @endif
    </p>
    <div class="course-grid club-ranking">
        @forelse($clubs as $club)
            <article class="card club-card">
                <div class="club-card-heading">
                    @php($medal = ($clubTab !== 'similarity' || $club->similarity !== null) ? (['gold', 'silver', 'bronze'][$loop->iteration - 1] ?? null) : null)
                    <span class="club-rank @if($medal) club-rank--{{ $medal }} @endif">{{ $clubTab === 'similarity' && $club->similarity === null ? '—' : ($loop->iteration).'位' }}</span>
                    <span class="muted small">登録メンバー {{ $club->member_count }}人</span>
                </div>
                <h2>{{ $club->name }}</h2>
                @if($club->catalog_image_url)
                    <div class="club-cover">
                        <img src="{{ $club->catalog_image_url }}" alt="{{ $club->name }}の紹介画像" @if($club->catalog_image_width) width="{{ $club->catalog_image_width }}" height="{{ $club->catalog_image_height }}" @endif loading="lazy">
                    </div>
                @endif
                <p class="club-meta"><span class="pill pill--club">{{ $club->category }}</span>@foreach($club->tags as $tag)<span class="pill">{{ $tag }}</span>@endforeach</p>
                @if($club->contact_url || $club->website_url)
                    <div class="club-links">
                        @if($club->contact_url)<a class="button secondary" href="{{ $club->contact_url }}" target="_blank" rel="noopener noreferrer">SNSなどで連絡する →</a>@endif
                        @if($club->website_url)<a class="button secondary" href="{{ $club->website_url }}" target="_blank" rel="noopener noreferrer">ホームページ →</a>@endif
                    </div>
                @endif
                <div class="club-score">
                    @if($clubTab === 'similarity')
                        @if($club->similarity !== null)
                            <strong>{{ round($club->similarity, 1) }}<small>%</small></strong>
                            <span>あなたと同じ {{ $me->personality }}<br>{{ $club->matching_count }}人 / 診断登録 {{ $club->diagnosed_count }}人</span>
                        @else
                            <span>診断結果がまだ登録されていません</span>
                        @endif
                    @else
                        <strong>{{ number_format($club->review_count) }}<small>件</small></strong>
                        <span>授業・教授の口コミ</span>
                    @endif
                </div>
                @if($clubTab === 'similarity')<p class="muted small">口コミ投稿数 {{ number_format($club->review_count) }}件</p>@endif
                <details class="club-types">
                    <summary>性格タイプの内訳 · 登録 {{ $club->diagnosed_count }}人 / 未登録 {{ $club->member_count - $club->diagnosed_count }}人</summary>
                    <p class="muted small">割合は診断結果の登録者を対象に計算しています。</p>
                    @forelse($club->distribution as $type)
                        <div class="club-type-row">
                            <span>{{ $type->type }}</span>
                            <meter min="0" max="100" value="{{ $type->percentage }}" aria-label="{{ $type->type }}の割合">{{ $type->percentage }}%</meter>
                            <span>{{ $type->count }}人 · {{ $type->percentage }}%</span>
                        </div>
                    @empty
                        <p class="muted small">メンバーが診断結果を登録すると、人数と割合が表示されます。</p>
                    @endforelse
                </details>
                @if($club->posts->isNotEmpty())
                    @include('campus.components.club-post', ['post' => $club->posts->first()])
                    @if($club->posts->count() > 1)
                        <details>
                            <summary>以前の活動・募集情報（{{ $club->posts->count() - 1 }}件）</summary>
                            @foreach($club->posts->skip(1) as $post)
                                @include('campus.components.club-post', ['post' => $post])
                            @endforeach
                        </details>
                    @endif
                @else
                    <p class="muted small">活動・募集情報はまだありません。</p>
                @endif
                <details class="club-edit">
                    <summary>サークル情報を編集</summary>
                    <p class="muted small">公式一覧に登録された団体名は変更できません。カテゴリ・タグ・連絡先・紹介画像を変更するには編集用パスワードが必要です。</p>
                    <form method="post" action="/campus/club-catalog/{{ $club->catalog_id }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <label>カテゴリ<input name="category" value="{{ old('category', $club->category) }}" required maxlength="40"></label>
                        <label>タグ（カンマ区切り）<input name="tags" value="{{ old('tags', implode('、', $club->tags)) }}" maxlength="255"></label>
                        <label>連絡先URL（任意）<input type="url" name="contact_url" value="{{ old('contact_url', $club->contact_url) }}" maxlength="2048" placeholder="https://www.instagram.com/..."></label>
                        <label>ホームページURL（任意）<input type="url" name="website_url" value="{{ old('website_url', $club->website_url) }}" maxlength="2048" placeholder="https://..."></label>
                        <label>紹介画像（任意・差し替え）<input type="file" name="image" accept="image/jpeg,image/png,image/webp" aria-describedby="club-catalog-image-help"></label>
                        <p class="muted small" id="club-catalog-image-help">JPEG・PNG・WebP、5MB以下、1,600万画素以下。新しい画像を選ぶと現在の画像と差し替わります。</p>
                        <label>編集用パスワード<input type="password" name="password" required minlength="10" maxlength="128" autocomplete="off"></label>
                        <button class="button secondary" type="submit">サークル情報を更新する</button>
                    </form>
                </details>
            </article>
        @empty
            <div class="empty">サークル情報はまだありません。プロフィールに所属サークルを登録するか、活動・募集情報を投稿しましょう。</div>
        @endforelse
    </div>
@endif

<section class="card" id="club-post-form">
    <h2>サークル情報を投稿</h2>
    <p class="muted small">公式一覧に登録済みのサークルだけ投稿できます。連絡を受け付ける場合はSNS等のURLを入力してください。</p>
    <form method="post" action="/campus/club" enctype="multipart/form-data" data-club-form>
        @csrf
        <label>サークル名
            <select name="name" required aria-describedby="club-name-help">
                <option value="">選択してください</option>
                @foreach($clubs as $clubOption)
                    <option value="{{ $clubOption->name }}" @selected(old('name', $me->circle) === $clubOption->name)>{{ $clubOption->name }}（{{ $clubOption->category }}）</option>
                @endforeach
            </select>
        </label>
        <p class="muted small" id="club-name-help">追加のサークル名は登録できません。公式一覧の更新が必要な場合は管理者に依頼してください。</p>
        <label>活動内容・募集情報<textarea name="body" required minlength="10" maxlength="2000">{{ old('body') }}</textarea></label>
        <label>連絡先URL（Instagramなど・任意）<input type="url" name="contact_url" value="{{ old('contact_url') }}" maxlength="2048" placeholder="https://www.instagram.com/..."></label>
        <label>ホームページURL（任意）<input type="url" name="website_url" value="{{ old('website_url') }}" maxlength="2048" placeholder="https://..."></label>
        <label>活動の写真（任意）<input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple data-club-images aria-describedby="club-image-help club-image-error"></label>
        <p class="muted small" id="club-image-help">JPEG・PNG・WebP、各5MB・1,600万画素以下、最大4枚。長辺1600px以下に縮小して保存します。入力エラーで戻った場合は画像を選び直してください。</p>
        <p class="error" id="club-image-error" role="alert" hidden></p>
        <div class="club-image-preview" data-club-preview aria-live="polite"></div>
        <button type="button" class="text-button club-image-clear" data-club-clear hidden>画像の選択をクリア</button>
        <button class="button" type="submit">投稿する</button>
    </form>
</section>
<script src="/campus-assets/clubs.js" defer></script>

@endsection

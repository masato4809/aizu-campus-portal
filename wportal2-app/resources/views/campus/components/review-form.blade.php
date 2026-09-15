<section id="post-review" class="card">
<h1>授業の評価を投稿</h1>
<p class="muted">授業名・英語名・科目コードを入力し、候補から授業を選択してください。</p>
<div class="review-progress" id="review-progress">
<p class="review-step-indicator" aria-live="polite">ステップ 1 / 3：授業・評価（あと2ステップ）</p>
<progress style="width:100%;max-width:none;display:block" id="review-step-progress" max="3" value="1" aria-label="投稿までの進行状況">1 / 3</progress>
<div class="review-progress-labels"><span>1. 授業・評価</span><span>2. 授業情報</span><span>3. 確認・投稿</span></div>
</div>
<form method="post" action="/campus/review" id="course-review-form">
@csrf

<div class="review-step" data-step="1">
<h2>STEP 1：授業・評価</h2>

<label>授業を自動検索<input id="course-lookup" type="search" autocomplete="off" placeholder="例：プログラミング、PL01" aria-controls="course-options"></label>
<label data-summary="投稿する授業">投稿する授業<select id="course-options" name="course_id" required>
<option value="">授業を選択してください</option>
@foreach($courseOptions as $option)
<option value="{{ $option->id }}" data-professor="{{ $option->professor }}" data-year="{{ $option->academic_year }}" @selected((string) old('course_id') === (string) $option->id)>{{ $option->name }} / {{ $option->name_en }} · {{ $option->course_code }} · {{ $option->academic_year }}年度 · {{ $option->professor }}</option>
@endforeach
</select></label>
<p id="course-search-status" class="muted" role="status"></p>
<p id="selected-professor" class="muted"></p>
<noscript><p>授業の選択欄から選んでください。実際に受講した年度を入力してください。</p></noscript>
<label data-summary="ユーザの属性">ユーザの属性<select name="user_type" required><option value="">選択してください</option>@foreach($userTypes as $key => $label)<option value="{{ $key }}" @selected(old('user_type') === $key)>{{ $label }}</option>@endforeach</select></label>

<fieldset class="review-ratings"><legend>評価（全て1〜5）</legend><div class="rating-inputs">
@foreach($ratings as $key => $label)
<div class="rating-item" data-summary="{{ $label }}"><div class="rating-header"><span class="rating-label">{{ $label }}</span></div><div class="rating-scale" role="radiogroup" aria-label="{{ $label }}">
@for($i = 1; $i <= 5; $i++)<label class="rating-choice"><input type="radio" name="{{ $key }}" value="{{ $i }}" @checked(old($key, 3) == $i) required><span>{{ $i }}</span></label>@endfor
</div></div>
@endforeach
</div></fieldset>
<label data-summary="授業のコメント">授業のコメント（20〜500字）<textarea name="body" required minlength="20" maxlength="500">{{ old('body') }}</textarea></label>
<button type="button" class="button" data-step-next>次へ：テスト・授業情報を入力 →</button>
</div>

<div class="review-step" data-step="2">
<h2>STEP 2：テスト・授業情報</h2>
<div class="form-grid">
<label data-summary="過去問">過去問<select name="has_past_exam" required><option value="">選択してください</option><option value="1" @selected((string) old('has_past_exam') === '1')>あり</option><option value="0" @selected((string) old('has_past_exam') === '0')>なし</option></select></label>
<label data-summary="課題の量">課題の量<select name="assignment_load" required><option value="">選択してください</option>@foreach($levels as $key => $value)<option value="{{ $key }}" @selected(old('assignment_load') === $key)>{{ $value }}</option>@endforeach</select></label>
<label data-summary="リモート割合">リモート割合<select name="remote_level" required><option value="">選択してください</option>@foreach($levels as $key => $value)<option value="{{ $key }}" @selected(old('remote_level') === $key)>{{ $value }}</option>@endforeach</select></label>
<label data-summary="履修人数">履修人数<select name="class_size" required><option value="">選択してください</option>@foreach($classSizes as $key => $label)<option value="{{ $key }}" @selected(old('class_size') === $key)>{{ $label }}</option>@endforeach</select></label>
<label data-summary="受講年度">受講年度（例：2024・2025・2026）<input id="course-academic-year" type="number" name="academic_year" value="{{ old('academic_year', now()->year) }}" min="1900" max="{{ now()->year }}" required></label>
<label data-summary="学期">学期<select name="semester" required><option value="">選択してください</option>@foreach($semesters as $key => $label)<option value="{{ $key }}" @selected(old('semester') === $key)>{{ $label }}</option>@endforeach</select></label>
</div>
<fieldset data-summary="評価方式の割合"><legend>評価方式の割合（%・合計100%まで）</legend>
<div class="form-grid form-grid-even">
@foreach($evaluationMethods as $key => $label)
<label>{{ $label }}の割合（%）<input type="number" name="{{ $key }}_weight" min="0" max="100" value="{{ old($key.'_weight') }}" placeholder="0"></label>
@endforeach
</div>
</fieldset>
<label data-summary="教材">教材（任意）<input name="materials" value="{{ old('materials') }}" maxlength="2000"></label>
<button type="button" class="button secondary" data-step-back>← 戻る</button>
<button type="button" class="button" data-step-next>次へ：入力内容の確認 →</button>
</div>

<div class="review-step" data-step="3">
<h2>STEP 3：確認</h2>
<p class="muted">入力内容をご確認のうえ、投稿してください。修正する場合は「戻る」から編集できます。</p>
<div id="review-summary" class="review-summary"></div>
<button type="button" class="button secondary" data-step-back>← 戻る</button>
<button class="button">評価を投稿する</button>
</div>

</form>
</section>
<script src="/campus-assets/course-autocomplete.js?v={{ filemtime(public_path('campus-assets/course-autocomplete.js')) }}" defer></script>
<script src="/campus-assets/course-review-wizard.js?v={{ filemtime(public_path('campus-assets/course-review-wizard.js')) }}" defer></script>

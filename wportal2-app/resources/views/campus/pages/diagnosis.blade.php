@extends('campus.layout')
@section('content')
@include('campus.components.page-intro')
<p class="notice">性格・学び方診断は必須です。すべて回答して保存すると、授業検索などを利用できます。</p>

<form method="post" action="/campus/diagnosis">@csrf
<section class="card">
<h2>性格診断</h2>
<p class="muted small">それぞれ近いと思う方を選んでください。サークルの相性がわかりやすくなります。</p>
@foreach($questions as $i => $question)<div class="quiz-question">
<p>{{ $i + 1 }}. {{ $question['text'] }}</p>
<div class="quiz-options">
<label><input type="radio" name="q{{ $i + 1 }}" value="a" @checked(old('q'.($i + 1)) === 'a') required> {{ $question['a'] }}</label>
<label><input type="radio" name="q{{ $i + 1 }}" value="b" @checked(old('q'.($i + 1)) === 'b')> {{ $question['b'] }}</label>
</div>
</div>@endforeach
</section>

<section class="card">
<h2>目標診断</h2>
<label>授業で大事にしたい目標は？<select name="goal_orientation" required>
<option value="">選択してください</option>@foreach($goalOrientations as $key => $label)<option value="{{ $key }}" @selected(old('goal_orientation', $me->goal_orientation) === $key)>{{ $label }}</option>@endforeach
</select>
</label>
</section>

<button class="button">診断結果を保存する</button>
</form>

@endsection

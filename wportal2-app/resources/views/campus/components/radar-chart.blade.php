<div class="rating-layout">
<svg class="radar" viewBox="0 0 240 220" role="img" aria-label="{{ $course->name }}の5項目平均評価。数値は横の一覧に表示">
@for($level=1;$level<=5;$level++)
<polygon points="@for($i=0;$i<5;$i++){{ 120+78*$level/5*cos(deg2rad(-90+$i*72)) }},{{ 108+78*$level/5*sin(deg2rad(-90+$i*72)) }} @endfor" fill="none" stroke="#dfe7e5"/>
@endfor
<polygon points="@foreach(array_keys($ratings) as $i=>$key){{ 120+78*$course->averages[$key]/5*cos(deg2rad(-90+$i*72)) }},{{ 108+78*$course->averages[$key]/5*sin(deg2rad(-90+$i*72)) }} @endforeach
" fill="#23927b33" stroke="#23836e" stroke-width="2"/>
@foreach(array_values($ratings) as $i=>$label)<text x="{{ 120+98*cos(deg2rad(-90+$i*72)) }}" y="{{ 112+98*sin(deg2rad(-90+$i*72)) }}" text-anchor="middle" font-size="12" fill="#536761">{{ $i + 1 }}</text>@endforeach

</svg>
<dl class="ratings">@foreach($ratings as $key=>$label)<div>
<dt>{{ array_search($key, array_keys($ratings), true) + 1 }}. {{ $label }}</dt>
<dd>{{ number_format($course->averages[$key], 1) }}</dd>
</div>@endforeach
</dl>
</div>

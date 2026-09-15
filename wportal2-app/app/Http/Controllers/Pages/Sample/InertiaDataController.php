<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages\Sample;

use App\Http\Controllers\Controller;
use App\Models\App\Trn\TrnUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Inertia\Response;

class InertiaDataController extends Controller
{
    /**
     * wportal2-app/routes/web.phpでURL→コントローラーの紐付けを行っている.
     * '/sample/inertia_data' -> '\App\Http\Controllers\Pages\Sample\InertiaDataController@invoke'
     *
     * このPHP側の処理から、React側のコンポーネントを呼び出す.
     * データは数パターン用意.
     */
    public function invoke(): Response
    {
        /**
         * ① 固定の配列をサーバーからReactへ渡す
         */
        $fixArray      = [
            [
                'name' => '山田太郎',
                'age'  => 20,
            ],
            [
                'name' => '田中花子',
                'age'  => 30,
            ],
        ];

        /**
         * ② DBから直接のSQLで取得してReactへ渡す.
         */
        $sqlArray      = DB::select('select * from trn_user limit 5');

        /**
         * ③ DBからEloquentで取得してReactへ渡す.
         */
        $eloquentArray = TrnUser::query()
            ->limit(5)
            ->get();

        /**
         * ④ DBから受け取ったデータを変換関数を通してからReactへ渡す
         * DBにはnullなどが入っている可能性があり、そのまま渡すのは安全ではない.
         * map関数は反復処理をしてCollectionとして返す関数.
         * eachでそれぞれ処理したデータを配列として戻している.
         */
        $payloadArray  = TrnUser::query()
            ->limit(5)
            ->get()
            ->map(function (Model $trnUser) {
                /** @var TrnUser $trnUser */
                return $trnUser->toPayload();
            })->toArray();

        /**
         * ⑤ ④と同等のデータだがProviderに保持する
         * メリットはpropsのリレーを行わずに利用箇所で参照できるようになる.
         */
        $providerArray = TrnUser::query()
            ->limit(5)
            ->get()
            ->map(function (Model $trnUser) {
                /** @var TrnUser $trnUser */
                return $trnUser->toPayload();
            })->toArray();

        /**
         * ⑥ ④と同等のデータだがProviderに保持する.
         * 保持の際にサーバー側の出力を信用せずにparse処理を通す.
         */
        $parseArray    = TrnUser::query()
            ->limit(5)
            ->get()
            ->map(function (Model $trnUser) {
                /** @var TrnUser $trnUser */
                return $trnUser->toPayload();
            })->toArray();

        /**
         * resources/script/Pages以下のSample/InertiaData/Index.tsxを呼び出す.
         *
         * キーバリューペアでデータを送る.
         *
         * React側はキー名でPropsとしてデータを受け取る事が出来る.
         */
        return $this->render('Sample/InertiaData/Index', [
            'fixArray'      => $fixArray,
            'sqlArray'      => $sqlArray,
            'eloquentArray' => $eloquentArray,
            'payloadArray'  => $payloadArray,
            'providerArray' => $providerArray,
            'parseArray'    => $parseArray,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Enum\App\EPages;
use App\Facades\External\GoogleCalendarService;
use App\Facades\Internal\PipeService;
use App\Facades\Models\App\Mst\MstGoodsService;
use App\Facades\Models\App\Trn\TrnShopAggregateService;
use App\Facades\Models\App\Trn\TrnUserService;
use App\Http\Controllers\Controller;
use App\Models\App\Mst\MstGoods;
use App\Pipe\App\PipeAppShopLunchTicket;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Throwable;

class ShopController extends Controller
{
    /**
     * @throws Exception
     */
    public function invoke(Request $request): Response
    {
        return $this->render('Shop/Index', [

            // 商品情報.
            'mstGoodsList' => fn () => $this->getMstGoodsList($request, []),
        ]);
    }

    /**
     * ランチチケット予約実行.
     *
     * @throws Throwable
     */
    public function lunch_ticket(Request $request): RedirectResponse
    {
        // パイプ作成.
        /** @var PipeAppShopLunchTicket $pipe */
        $pipe = PipeService::insertNewPipe(
            new PipeAppShopLunchTicket($request->toArray())
        );

        // バリデーションチェック.
        if (! PipeService::isValid()) {
            return redirect()->action(EPages::SHOP->getInvokePath());
        }

        // データ準備.
        $pipe->prepare();
        if (is_null($pipe->start) || is_null($pipe->end)) {
            throw new Exception('Invalid date');
        }

        // 更新処理.
        DB::transaction(function () use ($pipe) {
            // チケット代金の消化.
            TrnUserService::updateByPipe();

            // 売れ行き情報の記録.
            TrnShopAggregateService::updateByPipe();

            $description = <<<EOF
【{$pipe->mstGoods?->name}】の予約が入りました。
※{$pipe->mstGoods?->price}＄チケット

{$pipe->mstGoods?->explain}

※履行されない場合も＄の返金はございませんのでご注意ください
EOF;

            // rakumoのブロック実行.
            GoogleCalendarService::blockSchedule(
                $pipe->authUser?->email,
                new Collection([(string) $pipe->mstGoods?->author_email]),
                $pipe->start,
                $pipe->end,
                $pipe->mstGoods?->name,
                '直接ご相談ください',
                $description
            );
        });

        return redirect()->action(EPages::SHOP->getInvokePath());
    }

    /**
     * 商品マスタ取得.
     *
     * @param  array<mixed>  $with
     * @return array<mixed>
     */
    private function getMstGoodsList(Request $request, array $with = []): array
    {
        return MstGoodsService::list($with)
            ->map(fn (MstGoods $mstGoods) => $mstGoods->toPayload($with))
            ->toArray();
    }
}

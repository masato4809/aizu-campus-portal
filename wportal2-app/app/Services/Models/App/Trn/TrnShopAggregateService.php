<?php

declare(strict_types=1);

namespace App\Services\Models\App\Trn;

use App\Enum\App\EPipeKind;
use App\Facades\Internal\PipeService;
use App\Models\App\Trn\TrnShopAggregate;
use App\Pipe\App\PipeAppShopLunchTicket;
use App\Pipe\PipeBase;
use App\Services\Models\ModelServiceBase;
use Exception;
use Throwable;

class TrnShopAggregateService extends ModelServiceBase
{
    /**
     * パイプによる更新.
     *
     * @throws Throwable
     */
    public function updateByPipe(): void
    {
        foreach (PipeService::getPipes(__CLASS__) as $pipe) {
            $kind = $pipe->getKind();
            match ($kind) {
                EPipeKind::APP_SHOP_LUNCH_TICKET => $this->execAppShopLunchTicket($pipe),
                default                          => throw new Exception,
            };
            $pipe->onUpdate(__CLASS__);
        }
    }

    /**
     * ランチチケット予約.
     *
     * @throws Exception
     * @throws Throwable
     */
    private function execAppShopLunchTicket(
        PipeBase $pipe
    ): void {
        /** @var PipeAppShopLunchTicket $pipe */
        // 新規集計データを作成.
        $shopAggregate               = new TrnShopAggregate;
        $shopAggregate->mst_goods_id = $pipe->mstGoods?->id;
        $shopAggregate->trn_user_id  = $pipe->authUser?->TrnUser?->id;

        // 保存.
        $this->insertOrFail($shopAggregate);
    }
}

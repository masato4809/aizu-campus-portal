<?php

declare(strict_types=1);

namespace App\Services\Models\App\Trn;

use App\Enum\App\Qmart\EQmartItemStatus;
use App\Models\App\Trn\TrnQmartItem;
use App\Services\Models\ModelServiceBase;
use Illuminate\Support\Collection;

class TrnQmartItemService extends ModelServiceBase
{
    /**
     * オフセットページネーション情報の取得.
     *
     * @param  array<mixed>  $with
     * @return array<string, mixed>
     */
    public function offsetPagination(
        int $index,
        int $step,
        array $with = []
    ): array {
        // リスト取得.
        /** @var Collection<int, TrnQmartItem> $trnQmartItemList */
        $trnQmartItemList      = TrnQmartItem::query()
            ->with($with)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->offset($index * $step)
            ->limit($step)
            ->get();

        // カウント取得.
        $trnQmartItemListCount = TrnQmartItem::count();

        return [
            'list'  => $trnQmartItemList->map(function (TrnQmartItem $trnQmartItem) use ($with) {
                return $trnQmartItem->toPayload($with);
            })->toArray(),
            'count' => $trnQmartItemListCount,
        ];
    }

    /**
     * ステータス別のオフセットページネーション情報の取得.
     *
     * @param  array<mixed>  $with
     * @return array<string, mixed>
     */
    public function offsetPaginationByStatus(
        EQmartItemStatus $status,
        int $index,
        int $step,
        array $with = []
    ): array {
        // リスト取得.
        /** @var Collection<int, TrnQmartItem> $trnQmartItemList */
        $trnQmartItemList      = TrnQmartItem::query()
            ->with($with)
            ->where('status', $status->value)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->offset($index * $step)
            ->limit($step)
            ->get();

        // カウント取得.
        $trnQmartItemListCount = TrnQmartItem::query()
            ->where('status', $status->value)
            ->count();

        return [
            'list'  => $trnQmartItemList->map(function (TrnQmartItem $trnQmartItem) use ($with) {
                return $trnQmartItem->toPayload($with);
            })->toArray(),
            'count' => $trnQmartItemListCount,
        ];
    }
}

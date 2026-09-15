<?php

declare(strict_types=1);

namespace App\Services\Models\App\Mst;

use App\Enum\App\EEnableFlag;
use App\Enum\Mst\EMstReward;
use App\Models\App\Mst\MstReward;
use App\Services\Models\ModelServiceBase;
use Illuminate\Support\Collection;

class MstRewardService extends ModelServiceBase
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
        array $with = [],
    ): array {
        // リスト取得.
        /** @var Collection<int, MstReward> $mstRewardList */
        $mstRewardList      = MstReward::query()
            ->with($with)
            ->alive()
            ->offset($index * $step)
            ->limit($step)
            ->get();

        // カウント取得.
        $mstRewardListCount = MstReward::query()
            ->alive()
            ->count();

        return [
            'list'  => $mstRewardList->map(function (MstReward $mstReward) use ($with) {
                return $mstReward->toPayload($with);
            })->toArray(),
            'count' => $mstRewardListCount,
        ];
    }

    /**
     * 再達成可能かどうか.
     */
    public function isEnableRepeat(EMstReward $eMstReward): bool
    {
        $mstReward = $this->findById($eMstReward);
        if (is_null($mstReward)) {
            return false;
        }

        return $mstReward->e_enable_repeat === EEnableFlag::ENABLE->value;
    }

    /**
     * ID指定検索.
     */
    public function findById(EMstReward $eMstReward): ?MstReward
    {
        /** @var MstReward|null */
        return MstReward::query()
            ->where('id', $eMstReward->value)
            ->first();
    }
}

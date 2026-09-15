<?php

declare(strict_types=1);

namespace App\Services\Models\App\Mst;

use App\Models\App\Mst\MstGoods;
use App\Services\Models\ModelServiceBase;
use Illuminate\Support\Collection;

class MstGoodsService extends ModelServiceBase
{
    /**
     * 全取得.
     *
     * @param  array<mixed>  $with
     * @return Collection<int, MstGoods>
     */
    public function list(
        array $with = []
    ): Collection {
        /** @var Collection<int, MstGoods> */
        return MstGoods::query()
            ->alive()
            ->get();
    }

    /**
     * ID指定取得.
     */
    public function findOrFail(int $goodsId): MstGoods
    {
        /** @var MstGoods */
        return MstGoods::query()
            ->alive()
            ->findOrFail($goodsId);
    }
}

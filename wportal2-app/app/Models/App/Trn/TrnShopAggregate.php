<?php

declare(strict_types=1);

namespace App\Models\App\Trn;

use App\Models\App\ModelBase;
use App\Trait\EagerLoadHelper;

/**
 * @mixin TrnShopAggregate
 *
 * @extends ModelBase<TrnShopAggregate>
 */
class TrnShopAggregate extends ModelBase
{
    use EagerLoadHelper;

    protected $table = 'trn_shop_aggregate';

    /**
     * レコードをGraphQLなどでの受け渡し用に変換.
     *
     * @param  array<mixed>  $with
     * @param  array<string>  $history
     * @return array<mixed>
     */
    public function toPayload(array $with = [], array $history = []): array
    {
        return [
            'id'         => $this->id                   ?? 0,
            'mstGoodsId' => $this->mst_goods_id         ?? 0,
            'trnUserId'  => $this->trn_user_id          ?? 0,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Models\App\Trn;

use App\Models\App\ModelBase;
use App\Trait\EagerLoadHelper;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin TrnQmartItemImage
 *
 * @extends ModelBase<TrnQmartItemImage>
 */
class TrnQmartItemImage extends ModelBase
{
    use EagerLoadHelper;

    protected $table   = 'trn_qmart_item_images';

    public $timestamps = false;

    /**
     * リレーション:TrnQmartItem（紐づくアイテム）
     *
     * @return HasOne<TrnQmartItem, covariant TrnQmartItemImage>
     */
    public function TrnQmartItem(): HasOne
    {
        return $this->hasOne(
            TrnQmartItem::class,
            'id',
            'qmart_item_id'
        );
    }

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
            'id'           => $this->id            ?? 0,
            'qmartItemId'  => $this->qmart_item_id ?? 0,
            'filePath'     => $this->file_path     ?? '',
            'sortOrder'    => $this->sort_order    ?? 0,

            // TrnQmartItem（紐づくアイテム）.
            'trnQmartItem' => $this->isExistKeyInWith($with, 'TrnQmartItem', $history)
                ? $this->TrnQmartItem?->toPayload($with, [...$history, 'TrnQmartItem'])
                : null,
        ];
    }
}

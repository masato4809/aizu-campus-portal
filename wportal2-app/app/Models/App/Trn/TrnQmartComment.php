<?php

declare(strict_types=1);

namespace App\Models\App\Trn;

use App\Models\App\ModelBase;
use App\Trait\EagerLoadHelper;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin TrnQmartComment
 *
 * @extends ModelBase<TrnQmartComment>
 */
class TrnQmartComment extends ModelBase
{
    use EagerLoadHelper;

    protected $table   = 'trn_qmart_comments';

    public $timestamps = true;

    public const UPDATED_AT = null;


    /**
     * リレーション:TrnQmartItem（紐づくアイテム）
     *
     * @return HasOne<TrnQmartItem, covariant TrnQmartComment>
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
     * リレーション:TrnUser（コメント投稿者）
     *
     * @return HasOne<TrnUser, covariant TrnQmartComment>
     */
    public function TrnUser(): HasOne
    {
        return $this->hasOne(
            TrnUser::class,
            'id',
            'user_id'
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
            'userId'       => $this->user_id       ?? 0,
            'comment'      => $this->comment       ?? '',
            'createdAt'    => $this->created_at    ?? '',

            // TrnQmartItem（紐づくアイテム）.
            'trnQmartItem' => $this->isExistKeyInWith($with, 'TrnQmartItem', $history)
                ? $this->TrnQmartItem?->toPayload($with, [...$history, 'TrnQmartItem'])
                : null,

            // TrnUser（コメント投稿者）.
            'trnUser'      => $this->isExistKeyInWith($with, 'TrnUser', $history)
                ? $this->TrnUser?->toPayload($with, [...$history, 'TrnUser'])
                : null,
        ];
    }
}

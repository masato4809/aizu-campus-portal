<?php

declare(strict_types=1);

namespace App\Models\App\Trn;

use App\Models\App\ModelBase;
use App\Trait\EagerLoadHelper;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin TrnQmartItem
 *
 * @extends ModelBase<TrnQmartItem>
 */
class TrnQmartItem extends ModelBase
{
    use EagerLoadHelper, SoftDeletes;

    protected $table = 'trn_qmart_items';

    /**
     * リレーション:TrnUser（出品者）
     *
     * @return HasOne<TrnUser, covariant TrnQmartItem>
     */
    public function TrnUser(): HasOne
    {
        return $this->hasOne(
            TrnUser::class,
            'id',
            'user_id'
        )->alive();

    }

    /**
     * リレーション:TrnQmartItemImage（画像）
     *
     * @return HasMany<TrnQmartItemImage, covariant TrnQmartItem>
     */
    public function TrnQmartItemImage(): HasMany
    {
        return $this->hasMany(
            TrnQmartItemImage::class,
            'qmart_item_id',
            'id'
        );
    }

    /**
     * リレーション:TrnQmartComment（コメント）
     *
     * @return HasMany<TrnQmartComment, covariant TrnQmartItem>
     */
    public function TrnQmartComment(): HasMany
    {
        return $this->hasMany(
            TrnQmartComment::class,
            'qmart_item_id',
            'id'
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
            'id'                => $this->id          ?? 0,
            'userId'            => $this->user_id     ?? 0,
            'title'             => $this->title       ?? '',
            'description'       => $this->description ?? '',
            'price'             => $this->price       ?? 0,
            'status'            => $this->status      ?? 0,
            'createdAt'         => $this->created_at  ?? '',
            'updatedAt'         => $this->updated_at  ?? '',

            // TrnUser（出品者）.
            'trnUser'           => $this->isExistKeyInWith($with, 'TrnUser', $history)
                ? $this->TrnUser?->toPayload($with, [...$history, 'TrnUser'])
                : null,

            // TrnQmartItemImage（画像）.
            'trnQmartItemImage' => $this->isExistKeyInWith($with, 'TrnQmartItemImage', $history)
                ? $this->TrnQmartItemImage->map(
                    fn (TrnQmartItemImage $image) => $image->toPayload($with, [...$history, 'TrnQmartItemImage'])
                )
                : [],

            // TrnQmartComment（コメント）.
            'trnQmartComment'   => $this->isExistKeyInWith($with, 'TrnQmartComment', $history)
                ? $this->TrnQmartComment->map(
                    fn (TrnQmartComment $comment) => $comment->toPayload($with, [...$history, 'TrnQmartComment'])
                )
                : [],
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Models\App\Trn;

use App\Models\App\ModelBase;
use App\Trait\EagerLoadHelper;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin TrnShuffleLunchGroup
 *
 * @extends ModelBase<TrnShuffleLunchGroup>
 */
class TrnShuffleLunchGroup extends ModelBase
{
    use EagerLoadHelper;

    protected $table = 'trn_shuffle_lunch_group';

    /**
     * リレーション:TrnUser
     *
     * @return HasOne<TrnUser, covariant TrnShuffleLunchGroup>
     */
    public function TrnUser(): HasOne
    {
        return $this->hasOne(
            TrnUser::class,
            'id',
            'trn_user_id'
        )->alive();
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
            'id'            => $this->id                 ?? 0,
            'eventDate'     => $this->event_date         ?? '',
            'eventTimeZone' => $this->event_time_zone    ?? 0,
            'groupId'       => $this->group_id           ?? 0,
            'trnUserId'     => $this->trn_user_id        ?? 0,
            'rakumoBlocked' => $this->rakumo_blocked     ?? 0,

            'trnUser'       => $this->isExistKeyInWith($with, 'TrnUser', $history)
                ? $this->TrnUser?->toPayload($with, [...$history, 'TrnUser'])
                : null,
        ];
    }
}

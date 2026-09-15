<?php

declare(strict_types=1);

namespace App\Models\App\Trn;

use App\Models\App\ModelBase;
use App\Models\App\Mst\MstReward;
use App\Trait\EagerLoadHelper;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin TrnUserReward
 *
 * @extends ModelBase<TrnUserReward>
 */
class TrnUserReward extends ModelBase
{
    use EagerLoadHelper;

    protected $table = 'trn_user_reward';

    /**
     * リレーション:MstReward
     *
     * @return HasOne<MstReward, covariant TrnUserReward>
     */
    public function MstReward(): HasOne
    {
        return $this->hasOne(
            MstReward::class,
            'id',
            'mst_reward_id'
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
            'id'                => $this->id                               ?? 0,
            'trnUserId'         => $this->trn_user_id                      ?? 0,
            'mstRewardId'       => $this->mst_reward_id                    ?? 0,
            'achievementCount'  => $this->achievement_count                ?? 0,
            'receivedCount'     => $this->received_count                   ?? 0,
            'achievedAt'        => $this->achieved_at                      ?? '',
            'receivedAt'        => $this->received_at                      ?? '',

            'mstReward'         => $this->isExistKeyInWith($with, 'MstReward', $history)
                ? $this->MstReward?->toPayload($with, [...$history, 'MstUser']) : null,
        ];
    }
}

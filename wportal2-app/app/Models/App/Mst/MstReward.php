<?php

declare(strict_types=1);

namespace App\Models\App\Mst;

use App\Models\App\ModelBase;
use App\Trait\EagerLoadHelper;

/**
 * @mixin MstReward
 *
 * @extends ModelBase<MstReward>
 */
class MstReward extends ModelBase
{
    use EagerLoadHelper;

    protected $table = 'mst_reward';

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
            'id'              => $this->id,
            'rewardKind'      => $this->reward_kind,
            'rewardRank'      => $this->reward_rank,
            'rewardName'      => $this->reward_name,
            'rewardExplain'   => $this->reward_explain,
            'rewardGold'      => $this->reward_gold,
            'eEnableRepeat'   => $this->e_enable_repeat,
        ];
    }
}

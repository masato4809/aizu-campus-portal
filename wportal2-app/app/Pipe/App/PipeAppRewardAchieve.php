<?php

declare(strict_types=1);

namespace App\Pipe\App;

use App\Enum\App\EPipeKind;
use App\Facades\Models\App\Trn\TrnUserRewardService;
use App\Models\App\Auth\AuthUser;
use App\Models\App\Trn\TrnUserReward;
use App\Pipe\PipeBase;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

final class PipeAppRewardAchieve extends PipeBase
{
    public int $trnUserRewardId = 0;

    public ?AuthUser $authUser;

    /** @var Collection<int, mixed>|null */
    public ?Collection $trnUserRewardIdList;

    public int $achieveGold     = 0;

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::APP_REWARD_ACHIEVE, $args);
    }

    /**
     * データ取得.
     *
     * @throws Exception
     */
    public function parse(): void
    {
        $this->trnUserRewardId     = $this->getArgAsInteger('trnUserRewardId');

        // 認証ユーザー取得.
        $this->authUser            = Auth::user();

        // 指定IDもしくは全ての獲得可能な報酬のリストを取得する.
        $trnUserRewardList         = TrnUserRewardService::listAchievable(
            $this->authUser?->TrnUser?->id ?: 0,
            $this->trnUserRewardId,
            [
                'MstReward',
            ]
        );

        // 獲得する報酬のIDを保持する.
        $this->trnUserRewardIdList = $trnUserRewardList->pluck('id');

        // 獲得するGoldの総額を保持する.
        $this->achieveGold         = (int) $trnUserRewardList->reduce(function ($carry, TrnUserReward $trnUserReward) {
            if (is_null($trnUserReward->MstReward)) {
                return $carry;
            }

            $achievableCount = max($trnUserReward->achievement_count - $trnUserReward->received_count, 0);

            return (int) $carry + $trnUserReward->MstReward->reward_gold * $achievableCount;
        }, 0);

        // その他獲得対象があれば.
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void {}
}

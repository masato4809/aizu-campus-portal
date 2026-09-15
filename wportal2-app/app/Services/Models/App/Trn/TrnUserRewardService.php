<?php

declare(strict_types=1);

namespace App\Services\Models\App\Trn;

use App\Enum\App\EAttendanceState;
use App\Enum\App\EPipeKind;
use App\Enum\Mst\EMstReward;
use App\Facades\Internal\PipeService;
use App\Facades\Models\App\Mst\MstRewardService;
use App\Models\App\Auth\AuthUser;
use App\Models\App\Trn\TrnUserReward;
use App\Pipe\App\PipeAppAttendanceCreate;
use App\Pipe\App\PipeAppPersonalSettingEditUpdate;
use App\Pipe\App\PipeAppRewardAchieve;
use App\Pipe\PipeBase;
use App\Services\Models\ModelServiceBase;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class TrnUserRewardService extends ModelServiceBase
{
    /**
     * パイプによる更新.
     *
     * @throws Throwable
     */
    public function updateByPipe(): void
    {
        foreach (PipeService::getPipes(__CLASS__) as $pipe) {
            $kind = $pipe->getKind();
            match ($kind) {
                EPipeKind::APP_ATTENDANCE_CREATE            => $this->execAppAttendanceStateCreate($pipe),
                EPipeKind::APP_REWARD_ACHIEVE               => $this->execAppRewardAchieve($pipe),
                EPipeKind::APP_PERSONAL_SETTING_EDIT_UPDATE => $this->execAppPersonalSettingEditUpdate($pipe),
                default                                     => throw new Exception,
            };
            $pipe->onUpdate(__CLASS__);
        }
    }

    /**
     * 勤怠生成.
     *
     * @throws Exception
     */
    private function execAppAttendanceStateCreate(
        PipeBase $pipe
    ): void {
        /** @var PipeAppAttendanceCreate $pipe */
        if (is_null($pipe->todayAttendanceStateList)) {
            throw new Exception('Invalid todayAttendanceStateList.');
        }

        // 出勤・退勤・休憩in/outが含まれているかチェックする.
        if (! EAttendanceState::isPerfectAttendance($pipe->todayAttendanceStateList)) {
            return;
        }

        // 本日の獲得が存在するなら処理しない.
        if ($this->isAchieveToday(
            $pipe->authUser?->TrnUser?->id ?: 0,
            EMstReward::CREATE_PERFECT_ATTENDANCE)
        ) {
            Log::info('獲得済み.');

            return;
        }

        // 達成処理を実施.
        $this->achieve(
            $pipe,
            $pipe->authUser?->TrnUser?->id ?: 0,
            EMstReward::CREATE_PERFECT_ATTENDANCE
        );
    }

    /**
     * 報酬の達成.
     *
     * @throws Exception|Throwable
     */
    private function execAppRewardAchieve(
        PipeBase $pipe
    ): void {
        /** @var PipeAppRewardAchieve $pipe */

        // 対象となる報酬を取得.
        /** @var Collection<int, TrnUserReward> $targetReward */
        $targetReward = TrnUserReward::query()
            ->where('trn_user_id', $pipe->authUser?->TrnUser?->id)
            ->whereIn('id', $pipe->trnUserRewardIdList)
            ->get();

        // 対象のフラグを更新.
        $targetReward->each(function (TrnUserReward $trnUserReward) {
            $trnUserReward->received_count = $trnUserReward->achievement_count;
            $trnUserReward->received_at    = now();
            $this->updateOrFail($trnUserReward);
        });
    }

    /**
     * 報酬チェック・No.2:前略プロフィール.
     *
     * @throws Exception
     */
    private function execAppPersonalSettingEditUpdate(
        PipeBase $pipe
    ): void {
        /** @var PipeAppPersonalSettingEditUpdate $pipe */

        // 報酬達成処理.
        $this->achieve(
            $pipe,
            $pipe->trnUserId,
            EMstReward::UPDATE_PROFILE
        );
    }

    /**
     * アカウント作成時に呼び出す処理.
     *
     * @throws Exception
     */
    public function createNewUser(
        int $trnUserId
    ): void {
        $model                    = new TrnUserReward;
        $model->trn_user_id       = $trnUserId;
        $model->mst_reward_id     = EMstReward::CREATE_ACCOUNT;
        $model->achievement_count = 1;
        $this->insertOrFail($model);
    }

    /**
     * オフセットページネーション情報の取得.
     *
     * @param  array<mixed>  $with
     * @return array<string, mixed>
     */
    public function offsetPagination(
        int $trnUserId,
        int $index,
        int $step,
        array $with = [],
    ): array {
        // リスト取得.
        /** @var Collection<int, TrnUserReward> $trnUserRewardList */
        $trnUserRewardList      = TrnUserReward::query()
            ->where('trn_user_id', $trnUserId)
            ->with($with)
            ->alive()
            ->offset($index * $step)
            ->limit($step)
            ->orderBy('mst_reward_id')
            ->get();

        // カウント取得.
        $trnUserRewardListCount = TrnUserReward::query()
            ->where('trn_user_id', $trnUserId)
            ->alive()
            ->count();

        return [
            'list'  => $trnUserRewardList->map(function (TrnUserReward $trnUserReward) use ($with) {
                return $trnUserReward->toPayload($with);
            })->toArray(),
            'count' => $trnUserRewardListCount,
        ];
    }

    /**
     * 指定ユーザーの指定報酬を獲得済みにする.
     *
     * @throws Exception
     */
    public function achieve(
        PipeBase $pipe,
        int $trnUserId,
        EMstReward $eMstReward
    ): void {
        // 対象となる報酬を取得.
        /** @var TrnUserReward|null $targetReward */
        $targetReward = TrnUserReward::query()
            ->where('mst_reward_id', $eMstReward->value)
            ->where('trn_user_id', $trnUserId)
            ->first();

        // 存在しない場合は新規レコードを作成.
        if (is_null($targetReward)) {
            $newReward                    = new TrnUserReward;
            $newReward->trn_user_id       = $trnUserId;
            $newReward->mst_reward_id     = $eMstReward->value;
            $newReward->achievement_count = 1;
            $newReward->achieved_at       = now();
            $this->insertOrFail($newReward);
        } elseif (MstRewardService::isEnableRepeat($eMstReward)) {
            // 再達成可能な場合はカウントを増やす.
            $targetReward->achievement_count++;
            $targetReward->achieved_at = now();
            $this->updateOrFail($targetReward);
        }

        // TODO: ActivityLogの形式にする.
        Log::info("Archive reward id:[$trnUserId] reward:[$eMstReward->value]");

        // TODO: 通知を$pipeに格納することを検討.
    }

    /**
     * 本日の達成があるかどうか.
     */
    public function isAchieveToday(int $trnUserId, EMstReward $eMstReward): bool
    {
        return TrnUserReward::query()
            ->where('trn_user_id', $trnUserId)
            ->where('mst_reward_id', $eMstReward->value)
            ->whereDate('achieved_at', now())
            ->exists();
    }

    /**
     * 指定した報酬が達成済み・獲得済みのいずれかか.
     */
    public function isExist(EMstReward $eMstReward): bool
    {
        return TrnUserReward::query()
            ->where('mst_reward_id', $eMstReward->value)
            ->exists();
    }

    /**
     * 達成可能な報酬をリストで取得.
     *
     * @param  array<mixed>  $with
     * @return Collection<int, TrnUserReward>
     */
    public function listAchievable(
        int $trnUserId,
        int $trnUserRewardId,
        array $with = [],
    ): Collection {
        /** @var Collection<int, TrnUserReward> */
        return TrnUserReward::query()
            ->where('trn_user_id', $trnUserId)
            ->where('achievement_count', '>', 'received_count')
            ->when($trnUserRewardId, fn ($query) => $query->where('id', $trnUserRewardId))
            ->with($with)
            ->alive()
            ->get();
    }

    /**
     * 達成可能な報酬の数を取得.
     */
    public function countAchievable(
        ?AuthUser $authUser,
    ): int {
        // TODO: キャッシュ化を検討する.

        // 対象となる報酬のリストを取得.
        /** @var Collection<int, TrnUserReward> $trnUserRewardList */
        $trnUserRewardList = TrnUserReward::query()
            ->where('trn_user_id', $authUser?->TrnUser?->id)
            ->where('achievement_count', '>', 'received_count')
            ->alive()
            ->get();

        return (int) $trnUserRewardList->reduce(function (int $carry, TrnUserReward $trnUserReward) {
            return $carry + ($trnUserReward->achievement_count - $trnUserReward->received_count);
        }, 0);
    }
}

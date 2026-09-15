<?php

declare(strict_types=1);

namespace App\Services\Models\App\Trn;

use App\Console\Commands\Batch\ShuffleLunchMatching\Group;
use App\Console\Commands\Batch\ShuffleLunchMatching\GroupPack;
use App\Enum\App\EEnableFlag;
use App\Enum\App\EPipeKind;
use App\Facades\Internal\PipeService;
use App\Models\App\Trn\TrnShuffleLunchGroup;
use App\Pipe\App\PipeAppShuffleLunchRakumo;
use App\Pipe\PipeBase;
use App\Services\Models\ModelServiceBase;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Throwable;

class TrnShuffleLunchGroupService extends ModelServiceBase
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
                EPipeKind::APP_SHUFFLE_LUNCH_RAKUMO   => $this->execAppShuffleLunchRakumo($pipe),
                default                               => throw new Exception,
            };
        }
    }

    /**
     * @throws Throwable
     */
    private function execAppShuffleLunchRakumo(
        PipeBase $pipe
    ): void {
        /** @var PipeAppShuffleLunchRakumo $pipe */
        $trnShuffleLunchGroupList = $this->listByIdList($pipe->idList);
        $trnShuffleLunchGroupList->each(function (TrnShuffleLunchGroup $trnShuffleLunchGroup) {
            $trnShuffleLunchGroup->rakumo_blocked = EEnableFlag::ENABLE->value;
            $this->updateOrFail($trnShuffleLunchGroup);
        });
    }

    /**
     * マッチ結果を削除する.
     *
     * @throws Throwable
     */
    public function clearGroup(): void
    {
        $existList = $this->listToday();
        $existList->each(function (TrnShuffleLunchGroup $group) {
            $this->deleteOrFail($group);
        });
    }

    /**
     * グループパックからランチグループを作成する.
     *
     * @throws Throwable
     */
    public function createByGroupPack(GroupPack $groupPack): void
    {
        /**
         * グループパックからランチグループを作成.
         *
         * @note 速度が遅い場合は、一括insertを検討する.
         */
        $groupPack
            ->getGroupList()
            ->each(function (Group $group) use ($groupPack) {
                // 各ユーザーを登録.
                $group->getTargetUserIdList()->each(function (int $trnUserId) use ($group, $groupPack) {
                    $newGroup                  = new TrnShuffleLunchGroup;
                    $newGroup->event_date      = Carbon::now();
                    $newGroup->event_time_zone = $groupPack->getEventTimeZone();
                    $newGroup->group_id        = $group->getGroupId();
                    $newGroup->trn_user_id     = $trnUserId;

                    $this->insertOrFail($newGroup);
                });
            });
    }

    /**
     * @param  array<mixed>  $with
     * @return Collection<int, TrnShuffleLunchGroup>
     */
    public function listToday(array $with = []): Collection
    {
        /** @var Collection<int, TrnShuffleLunchGroup> */
        return TrnShuffleLunchGroup::query()
            ->whereDate('event_date', Carbon::now())
            ->with($with)
            ->alive()
            ->get();
    }

    /**
     * @param  Collection<int, int>  $idList
     * @param  array<mixed>  $with
     * @return Collection<int, TrnShuffleLunchGroup>
     */
    public function listByIdList(Collection $idList, array $with = []): Collection
    {
        /** @var Collection<int, TrnShuffleLunchGroup> */
        return TrnShuffleLunchGroup::query()
            ->whereIn('id', $idList)
            ->with($with)
            ->alive()
            ->get();
    }
}

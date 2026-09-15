<?php

declare(strict_types=1);

namespace App\Services\Models\App\Trn;

use App\Enum\App\EArchiveLevel;
use App\Enum\App\EPipeKind;
use App\Facades\Internal\PipeService;
use App\Models\App\Trn\TrnAttendanceState;
use App\Pipe\App\Input\InputEvent;
use App\Pipe\App\PipeAppAttendanceCreate;
use App\Pipe\App\PipeAppAttendanceDelete;
use App\Pipe\App\PipeAppAttendanceEditUpdate;
use App\Pipe\PipeBase;
use App\Services\Models\ModelServiceBase;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Throwable;

class TrnAttendanceStateService extends ModelServiceBase
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
                EPipeKind::APP_ATTENDANCE_DELETE            => $this->execAppAttendanceStateDelete($pipe),
                EPipeKind::APP_ATTENDANCE_EDIT_UPDATE       => $this->execAppAttendanceEditUpdate($pipe),
                default                                     => throw new Exception,
            };
            $pipe->onUpdate(__CLASS__);
        }
    }

    /**
     * @throws Throwable
     */
    private function execAppAttendanceStateCreate(
        PipeBase $pipe
    ): void {
        /** @var PipeAppAttendanceCreate $pipe */

        // モデル作成.
        $new                            = new TrnAttendanceState;
        $new->trn_user_id               = $pipe->authUser?->TrnUser?->id;
        $new->e_attendance_state        = $pipe->eAttendanceState->value;

        // 保存
        $this->insertOrFail($new);

        // 本日の出退勤をすべて格納しておく.
        /** @var Collection<int, TrnAttendanceState> $stateList */
        $stateList                      = TrnAttendanceState::query()
            ->where('trn_user_id', $pipe->authUser?->TrnUser?->id)
            ->whereDate('created_at', Carbon::now())
            ->alive()
            ->get();
        $pipe->todayAttendanceStateList = $stateList;
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    private function execAppAttendanceStateDelete(
        PipeBase $pipe
    ): void {
        /** @var PipeAppAttendanceDelete $pipe */

        // 対象データを取得.
        /** @var TrnAttendanceState $target */
        $target                  = TrnAttendanceState::query()
            ->orderByDesc('created_at')
            ->where('trn_user_id', $pipe->authUser?->TrnUser?->id)
            ->alive()
            ->firstOrFail();

        // 対象データを削除.
        $target->e_archive_level = EArchiveLevel::ARCHIVE->value;

        // 削除を保存.
        $target->updateOrFail();
    }

    /**
     * 勤怠時間の更新.
     *
     * @throws Exception|Throwable
     */
    private function execAppAttendanceEditUpdate(
        PipeBase $pipe
    ): void {
        /** @var PipeAppAttendanceEditUpdate $pipe */
        $pipe->eventList->each(function (InputEvent $event) {
            $model             = $this->findByIdOrFail($event->uid);
            $model->updated_at = $event->updatedAt;
            $this->updateOrFail($model);
        });
    }

    public function findByIdOrFail(int $id): TrnAttendanceState
    {
        /** @var TrnAttendanceState */
        return TrnAttendanceState::query()
            ->alive()
            ->findOrFail($id);
    }

    /**
     * ユーザーIDと期間を指定して、出退勤情報を取得.
     *
     * @return Collection<int, TrnAttendanceState>
     */
    public function listByUserIdAndPeriod(
        int $trnUserId,
        Carbon $startOfMonth,
        Carbon $endOfMonth
    ): Collection {
        /** @var Collection<int, TrnAttendanceState> */
        return TrnAttendanceState::query()
            ->where('trn_user_id', $trnUserId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->alive()
            ->get();
    }
}

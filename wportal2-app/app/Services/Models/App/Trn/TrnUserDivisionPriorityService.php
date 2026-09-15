<?php

declare(strict_types=1);

namespace App\Services\Models\App\Trn;

use App\Enum\App\EPipeKind;
use App\Facades\Internal\PipeService;
use App\Models\App\Trn\TrnUserDivisionPriority;
use App\Pipe\App\PipeAppAttendanceDivisionPriority;
use App\Pipe\PipeBase;
use App\Services\Models\ModelServiceBase;
use Exception;
use Throwable;

class TrnUserDivisionPriorityService extends ModelServiceBase
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
                EPipeKind::APP_ATTENDANCE_DIVISION_PRIORITY => $this->execAppAttendanceDivisionPriority($pipe),
                default                                     => throw new Exception,
            };
            $pipe->onUpdate(__CLASS__);
        }
    }

    /**
     * @throws Throwable
     */
    private function execAppAttendanceDivisionPriority(
        PipeBase $pipe
    ): void {
        /** @var PipeAppAttendanceDivisionPriority $pipe */
        // 現在の設定の取得.
        $current = $this->findByUserIdAndDivisionId(
            $pipe->authUser?->TrnUser?->id ?: 0,
            $pipe->divisionId
        );

        if ($current) {
            // 更新の場合.
            $current->division_priority = $pipe->priority;

            $this->updateOrFail($current);
        } else {
            // 新規登録の場合.
            $new                    = new TrnUserDivisionPriority;
            $new->trn_user_id       = $pipe->authUser?->TrnUser?->id;
            $new->trn_division_id   = $pipe->divisionId;
            $new->division_priority = $pipe->priority;

            $this->insertOrFail($new);
        }
    }

    /**
     * ユーザーIDと課IDで検索.
     */
    public function findByUserIdAndDivisionId(
        int $trnUserId,
        int $trnDivisionId
    ): ?TrnUserDivisionPriority {
        /** @var TrnUserDivisionPriority|null */
        return TrnUserDivisionPriority::query()
            ->where('trn_user_id', $trnUserId)
            ->where('trn_division_id', $trnDivisionId)
            ->alive()
            ->first();
    }
}

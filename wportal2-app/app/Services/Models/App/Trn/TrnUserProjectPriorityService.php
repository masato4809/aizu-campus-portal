<?php

declare(strict_types=1);

namespace App\Services\Models\App\Trn;

use App\Enum\App\EPipeKind;
use App\Facades\Internal\PipeService;
use App\Models\App\Trn\TrnUserProjectPriority;
use App\Pipe\App\PipeAppAttendanceProjectPriority;
use App\Pipe\PipeBase;
use App\Services\Models\ModelServiceBase;
use Exception;
use Throwable;

class TrnUserProjectPriorityService extends ModelServiceBase
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
                EPipeKind::APP_ATTENDANCE_PROJECT_PRIORITY => $this->execAppAttendanceProjectPriority($pipe),
                default                                    => throw new Exception,
            };
            $pipe->onUpdate(__CLASS__);
        }
    }

    /**
     * @throws Throwable
     */
    private function execAppAttendanceProjectPriority(
        PipeBase $pipe
    ): void {
        /** @var PipeAppAttendanceProjectPriority $pipe */
        // 現在の設定の取得.
        $current = $this->findByUserIdAndProjectId(
            $pipe->authUser?->TrnUser?->id ?: 0,
            $pipe->projectId
        );

        if ($current) {
            // 更新の場合.
            $current->project_priority = $pipe->priority;

            $this->updateOrFail($current);
        } else {
            // 新規登録の場合.
            $new                   = new TrnUserProjectPriority;
            $new->trn_user_id      = $pipe->authUser?->TrnUser?->id;
            $new->trn_project_id   = $pipe->projectId;
            $new->project_priority = $pipe->priority;

            $this->insertOrFail($new);
        }
    }

    /**
     * ユーザーIDとプロジェクトIDで検索.
     */
    public function findByUserIdAndProjectId(
        int $trnUserId,
        int $trnProjectId
    ): ?TrnUserProjectPriority {
        /** @var TrnUserProjectPriority|null */
        return TrnUserProjectPriority::query()
            ->where('trn_user_id', $trnUserId)
            ->where('trn_project_id', $trnProjectId)
            ->alive()
            ->first();
    }
}

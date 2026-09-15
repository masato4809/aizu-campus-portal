<?php

declare(strict_types=1);

namespace App\Services\Models\App\Trn;

use App\Enum\App\EPipeKind;
use App\Facades\Internal\PipeService;
use App\Models\App\Trn\TrnProjectUser;
use App\Pipe\App\PipeAppProjectUserUpdate;
use App\Pipe\App\PipeAppUserDelete;
use App\Pipe\PipeBase;
use App\Services\Models\ModelServiceBase;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class TrnProjectUserService extends ModelServiceBase
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
                EPipeKind::APP_PROJECT_USER_UPDATE  => $this->execAppProjectUserUpdate($pipe),
                EPipeKind::APP_USER_DELETE          => $this->execAppUserDelete($pipe),
                default                             => throw new Exception,
            };
            $pipe->onUpdate(__CLASS__);
        }
    }

    /**
     * @throws Throwable
     */
    private function execAppProjectUserUpdate(
        PipeBase $pipe
    ): void {
        /** @var PipeAppProjectUserUpdate $pipe */
        if (is_null($pipe->trnUserIdList)) {
            throw new Exception('Invalid user list.');
        }

        // 現在の配列.
        /** @var Collection<int, int> $oldUserList */
        $oldUserList = TrnProjectUser::query()
            ->where('trn_project_id', $pipe->trnProjectId)
            ->pluck('trn_user_id');

        // 追加する対象.
        $addUserList = $pipe->trnUserIdList->diff($oldUserList)->values();
        $addUserList->each(function (int $trnUserId) use ($pipe) {
            $addUser                  = new TrnProjectUser;
            $addUser->trn_project_id  = $pipe->trnProjectId;
            $addUser->trn_user_id     = $trnUserId;
            $addUser->saveOrFail();
        });

        // 削除する対象.
        $delUserList = $oldUserList->diff($pipe->trnUserIdList);
        if (! $delUserList->isEmpty()) {
            TrnProjectUser::query()
                ->where('trn_project_id', $pipe->trnProjectId)
                ->whereIn('trn_user_id', $delUserList)
                ->delete();
        }

        // ログ出力.
        $delLog      = json_encode($delUserList->toArray());
        $addLog      = json_encode($addUserList->toArray());
        Log::info("execAppProjectUserUpdate del:$delLog add:$addLog");
    }

    /**
     * EPipeKind::APP_USER_DELETE
     */
    private function execAppUserDelete(
        PipeBase $pipe
    ): void {
        /** @var PipeAppUserDelete $pipe */

        // ユーザーを削除.
        TrnProjectUser::query()
            ->where('trn_user_id', $pipe->trnUserId)
            ->alive()
            ->delete();
    }

    /**
     * ユーザーID指定でリスト取得.
     *
     * @return Collection<int, TrnProjectUser>
     */
    public function listByTrnUserId(int $trnUserId): Collection
    {
        /** @var Collection<int, TrnProjectUser> */
        return TrnProjectUser::query()
            ->where('trn_user_id', $trnUserId)
            ->alive()
            ->get();
    }
}

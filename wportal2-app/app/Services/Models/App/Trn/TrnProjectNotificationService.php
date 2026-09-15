<?php

declare(strict_types=1);

namespace App\Services\Models\App\Trn;

use App\Enum\App\EPipeKind;
use App\Facades\Internal\PipeService;
use App\Models\App\Trn\TrnProjectNotification;
use App\Pipe\App\Input\InputNotification;
use App\Pipe\App\PipeAppProjectUpdate;
use App\Pipe\PipeBase;
use App\Services\Models\ModelServiceBase;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class TrnProjectNotificationService extends ModelServiceBase
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
                EPipeKind::APP_PROJECT_UPDATE => $this->execAppProjectUpdate($pipe),
                default                       => throw new Exception,
            };
            $pipe->onUpdate(__CLASS__);
        }
    }

    /**
     * @throws Throwable
     */
    private function execAppProjectUpdate(
        PipeBase $pipe
    ): void {
        /** @var PipeAppProjectUpdate $pipe */

        // 現在の設定を取得.
        $existIdList  = TrnProjectNotification::query()
            ->where('trn_project_id', $pipe->trnProjectId)
            ->alive()
            ->pluck('id');

        // 入力されたIDリストを取得.
        $inputIdList  = $pipe->notificationList->pluck('id');

        // 新規追加分.
        $newIdList    = $inputIdList->diff($existIdList);

        // 削除対象分.
        $removeIdList = $existIdList->diff($inputIdList);

        // 更新対象分.
        $updateIdList = $existIdList->diff($removeIdList);

        // 新規登録処理.
        $pipe->notificationList
            ->whereIn('id', $newIdList)
            ->filter(function (InputNotification $input) {
                return (bool) $input->notificationValue;
            })
            ->each(function (InputNotification $input) use ($pipe) {
                $new                     = new TrnProjectNotification;
                $new->trn_project_id     = $pipe->trnProjectId;
                $new->notification_type  = $input->eNotificationType->value;
                $new->notification_value = $input->notificationValue;
                $this->insertOrFail($new);
            });

        // 削除処理.
        if ($removeIdList->isNotEmpty()) {
            TrnProjectNotification::query()
                ->whereIn('id', $removeIdList)
                ->archive();
        }

        // 更新処理.
        $pipe->notificationList
            ->whereIn('id', $updateIdList)
            ->filter(function (InputNotification $input) {
                return (bool) $input->notificationValue;
            })
            ->each(function (InputNotification $input) {
                $model                     = $this->findByIdOrFail($input->id);
                $model->notification_value = $input->notificationValue;
                $this->updateOrFail($model);
            });
    }

    /**
     * IDによる検索(orFail)
     *
     * @param  array<mixed>  $with
     */
    public function findByIdOrFail(
        int $id,
        array $with = []
    ): TrnProjectNotification {
        /** @var TrnProjectNotification */
        return TrnProjectNotification::query()
            ->with($with)
            ->alive()
            ->findOrFail($id);
    }

    /**
     * ID配列指定によるリスト取得.
     *
     * @param  array<int>  $projectIdList
     * @return Collection<int, TrnProjectNotification>
     */
    public function listByTrnProjectId(array $projectIdList): Collection
    {
        /** @var Collection<int, TrnProjectNotification> */
        return TrnProjectNotification::query()
            ->whereIn('trn_project_id', $projectIdList)
            ->alive()
            ->get();
    }
}

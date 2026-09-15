<?php

declare(strict_types=1);

namespace App\Services\Models\App\Trn;

use App\Enum\App\EPipeKind;
use App\Enum\App\GoodJob\ETargetType;
use App\Facades\Internal\PipeService;
use App\Models\App\Trn\TrnGoodJob;
use App\Pipe\App\PipeAppGoodJobCreate;
use App\Pipe\PipeBase;
use App\Services\Models\ModelServiceBase;
use Exception;
use Illuminate\Support\Collection;
use Throwable;

class TrnGoodJobService extends ModelServiceBase
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
                EPipeKind::APP_GOOD_JOB_CREATE => $this->execAppGoodJobCreate($pipe),
                default                        => throw new Exception,
            };
            $pipe->onUpdate(__CLASS__);
        }
    }

    /**
     * 感謝の作成.
     *
     * @throws Throwable
     */
    private function execAppGoodJobCreate(
        PipeBase $pipe
    ): void {
        /** @var PipeAppGoodJobCreate $pipe */
        $newModel                   = new TrnGoodJob;

        // 起票ユーザー.
        $newModel->trn_user_id      = $pipe->authUser?->TrnUser?->id ?: 0;

        // 送信元.
        $newModel->from_target_type = $pipe->fromTargetType->value;
        switch ($pipe->fromTargetType) {
            case ETargetType::USER:
                $newModel->from_trn_user_id     = $pipe->fromTrnUserId;
                $newModel->from_trn_division_id = 0;
                $newModel->from_trn_project_id  = 0;
                $newModel->from_other_label     = '';
                break;
            case ETargetType::DIVISION:
                $newModel->from_trn_user_id     = 0;
                $newModel->from_trn_division_id = $pipe->fromTrnDivisionId;
                $newModel->from_trn_project_id  = 0;
                $newModel->from_other_label     = '';
                break;
            case ETargetType::PROJECT:
                $newModel->from_trn_user_id     = 0;
                $newModel->from_trn_division_id = 0;
                $newModel->from_trn_project_id  = $pipe->fromTrnProjectId;
                $newModel->from_other_label     = '';
                break;
            case ETargetType::LABEL:
                $newModel->from_trn_user_id     = 0;
                $newModel->from_trn_division_id = 0;
                $newModel->from_trn_project_id  = 0;
                $newModel->from_other_label     = $pipe->fromOtherLabel;
                break;
        }

        // 送信先.
        $newModel->to_target_type   = $pipe->toTargetType->value;
        switch ($pipe->toTargetType) {
            case ETargetType::USER:
                $newModel->to_trn_user_id     = $pipe->toTrnUserId;
                $newModel->to_trn_division_id = 0;
                $newModel->to_trn_project_id  = 0;
                $newModel->to_other_label     = '';
                break;
            case ETargetType::DIVISION:
                $newModel->to_trn_user_id     = 0;
                $newModel->to_trn_division_id = $pipe->toTrnDivisionId;
                $newModel->to_trn_project_id  = 0;
                $newModel->to_other_label     = '';
                break;
            case ETargetType::PROJECT:
                $newModel->to_trn_user_id     = 0;
                $newModel->to_trn_division_id = 0;
                $newModel->to_trn_project_id  = $pipe->toTrnProjectId;
                $newModel->to_other_label     = '';
                break;
            case ETargetType::LABEL:
                $newModel->to_trn_user_id     = 0;
                $newModel->to_trn_division_id = 0;
                $newModel->to_trn_project_id  = 0;
                $newModel->to_other_label     = $pipe->toOtherLabel;
                break;
        }

        $newModel->title            = $pipe->title;
        $newModel->content          = $pipe->content;
        $this->insertOrFail($newModel);
    }

    /**
     * オフセットページネーション情報の取得.
     *
     * @param  array<mixed>  $with
     * @return array<string, mixed>
     */
    public function offsetPagination(
        int $index,
        int $step,
        array $with = []
    ): array {
        // リスト取得.
        /** @var Collection<int, TrnGoodJob> $trnGoodJobList */
        $trnGoodJobList      = TrnGoodJob::query()
            ->with($with)
            ->alive()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->offset($index * $step)
            ->limit($step)
            ->get();

        // カウント取得.
        $trnGoodJobListCount = TrnGoodJob::count();

        return [
            'list'  => $trnGoodJobList->map(function (TrnGoodJob $trnGoodJob) use ($with) {
                return $trnGoodJob->toPayload($with);
            })->toArray(),
            'count' => $trnGoodJobListCount,
        ];
    }
}

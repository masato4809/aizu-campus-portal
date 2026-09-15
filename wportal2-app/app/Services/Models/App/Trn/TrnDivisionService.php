<?php

declare(strict_types=1);

namespace App\Services\Models\App\Trn;

use App\Enum\App\EArchiveLevel;
use App\Enum\App\EPipeKind;
use App\Facades\Internal\PipeService;
use App\Models\App\Trn\TrnDivision;
use App\Pipe\App\PipeAppDivisionCreate;
use App\Pipe\App\PipeAppDivisionDelete;
use App\Pipe\App\PipeAppDivisionUpdate;
use App\Pipe\PipeBase;
use App\Services\Models\ModelServiceBase;
use Exception;
use Illuminate\Support\Collection;
use Throwable;

class TrnDivisionService extends ModelServiceBase
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
                EPipeKind::APP_DIVISION_UPDATE      => $this->execAppDivisionUpdate($pipe),
                EPipeKind::APP_DIVISION_DELETE      => $this->execAppDivisionDelete($pipe),
                EPipeKind::APP_DIVISION_CREATE      => $this->execAppDivisionCreate($pipe),
                default                             => throw new Exception,
            };
            $pipe->onUpdate(__CLASS__);
        }
    }

    private function execAppDivisionCreate(
        PipeBase $pipe
    ): void {
        /** @var PipeAppDivisionCreate $pipe */

        // 課の作成.
        $this->createNewDivision(
            $pipe->name,
            $pipe->explain,
        );
    }

    /**
     * 課の新規作成.
     */
    public function createNewDivision(string $divisionName, string $divisionExplain, int $id = 0): void
    {
        $trnDivision           = new TrnDivision;
        if ($id !==0) {
            $trnDivision->id   = $id;
        }
        $trnDivision->name     = $divisionName;
        $trnDivision->explain  = $divisionExplain;
        $trnDivision->saveOrFail();
    }

    /**
     * @throws Throwable
     */
    private function execAppDivisionUpdate(
        PipeBase $pipe
    ): void {
        /** @var PipeAppDivisionUpdate $pipe */

        // 課基本情報の更新.
        $trnDivision          = $this->findByIdOrFail($pipe->trnDivisionId);
        $trnDivision->name    = $pipe->name;
        $trnDivision->explain = $pipe->explain;

        // 保存.
        $this->updateOrFail($trnDivision);
    }

    /**
     * 課の削除
     *
     * @throws Exception
     */
    private function execAppDivisionDelete(
        PipeBase $pipe
    ): void {
        /** @var PipeAppDivisionDelete $pipe */

        // 対象データを取得.
        /** @var TrnDivision $target */
        $target                  = TrnDivision::query()
            ->where('id', $pipe->trnDivisionId)
            ->alive()
            ->firstOrFail();

        // 対象データを削除.
        $target->e_archive_level = EArchiveLevel::ARCHIVE->value;

        // 削除を保存.
        $this->updateOrFail($target);
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
        /** @var Collection<int, TrnDivision> $trnDivisionList */
        $trnDivisionList      = TrnDivision::query()
            ->with($with)
            ->alive()
            ->offset($index * $step)
            ->limit($step)
            ->get();

        // カウント取得.
        $trnDivisionListCount = TrnDivision::count();

        return [
            'list'  => $trnDivisionList->map(function (TrnDivision $trnDivision) use ($with) {
                return $trnDivision->toPayload($with);
            })->toArray(),
            'count' => $trnDivisionListCount,
        ];
    }

    /**
     * 全て取得.
     *
     * @param  array<string>  $with
     * @return Collection<int, TrnDivision>
     */
    public function list(array $with = []): Collection
    {
        /** @var Collection<int, TrnDivision> */
        return TrnDivision::query()
            ->with($with)
            ->alive()
            ->get();
    }

    /**
     * Idによる検索(orFail)
     *
     * @param  array<mixed>  $with
     */
    public function findByIdOrFail(
        int $id,
        array $with = []
    ): TrnDivision {
        /** @var TrnDivision */
        return TrnDivision::query()
            ->with($with)
            ->alive()
            ->findOrFail($id);
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Models\App\Trn;

use App\Enum\App\EArchiveLevel;
use App\Enum\App\EPipeKind;
use App\Facades\Internal\PipeService;
use App\Models\App\Trn\TrnProject;
use App\Pipe\App\PipeAppProjectCreate;
use App\Pipe\App\PipeAppProjectDelete;
use App\Pipe\App\PipeAppProjectUpdate;
use App\Pipe\PipeBase;
use App\Services\Models\ModelServiceBase;
use Exception;
use Illuminate\Support\Collection;
use Throwable;

class TrnProjectService extends ModelServiceBase
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
                EPipeKind::APP_PROJECT_DELETE => $this->execAppProjectDelete($pipe),
                EPipeKind::APP_PROJECT_CREATE => $this->execAppProjectCreate($pipe),
                default                       => throw new Exception,
            };
            $pipe->onUpdate(__CLASS__);
        }
    }

    /**
     * プロジェクトの作成.
     */
    private function execAppProjectCreate(
        PipeBase $pipe
    ): void {
        /** @var PipeAppProjectCreate $pipe */

        // プロジェクトの作成.
        $this->createNewProject(
            $pipe->name,
            $pipe->explain,
        );
    }

    /**
     * @throws Exception
     */
    private function execAppProjectDelete(
        PipeBase $pipe
    ): void {
        /** @var PipeAppProjectDelete $pipe */

        // 対象データを取得.
        /** @var TrnProject $target */
        $target                  = TrnProject::query()
            ->where('id', $pipe->trnProjectId)
            ->alive()
            ->firstOrFail();

        // 対象データを削除.
        $target->e_archive_level = EArchiveLevel::ARCHIVE->value;

        // 削除を保存.
        $this->updateOrFail($target);
    }

    /**
     * プロジェクトの新規追加
     *
     * @throws Throwable
     */
    public function createNewProject(string $projectName, string $projectExplain, int $id = 0): void
    {
        // ユーザーを作成.
        $trnProject                  = new TrnProject;
        if ($id !== 0) {
            $trnProject->id   = $id;
        }
        $trnProject->name            = $projectName;
        $trnProject->explain         = $projectExplain;
        $trnProject->saveOrFail();
    }

    /**
     * @throws Throwable
     */
    private function execAppProjectUpdate(
        PipeBase $pipe
    ): void {
        /** @var PipeAppProjectUpdate $pipe */

        // 課基本情報の更新.
        $trnProject          = $this->findByIdOrFail($pipe->trnProjectId);
        $trnProject->name    = $pipe->name;
        $trnProject->explain = $pipe->explain;

        // 保存.
        $this->updateOrFail($trnProject);
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
        /** @var Collection<int, TrnProject> $trnProjectList */
        $trnProjectList      = TrnProject::query()
            ->with($with)
            ->alive()
            ->offset($index * $step)
            ->limit($step)
            ->get();

        // カウント取得.
        $trnProjectListCount = TrnProject::count();

        return [
            'list'  => $trnProjectList->map(function (TrnProject $trnProject) use ($with) {
                return $trnProject->toPayload($with);
            })->toArray(),
            'count' => $trnProjectListCount,
        ];
    }

    /**
     * 全て取得.
     *
     * @param  array<string>  $with
     * @return Collection<int, TrnProject>
     */
    public function list(array $with = []): Collection
    {
        /** @var Collection<int, TrnProject> */
        return TrnProject::query()
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
    ): TrnProject {
        /** @var TrnProject */
        return TrnProject::query()
            ->with($with)
            ->alive()
            ->findOrFail($id);
    }
}

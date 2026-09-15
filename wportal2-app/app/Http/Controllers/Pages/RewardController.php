<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Enum\App\EPages;
use App\Facades\Internal\PipeService;
use App\Facades\Models\App\Mst\MstRewardService;
use App\Facades\Models\App\Trn\TrnUserRewardService;
use App\Facades\Models\App\Trn\TrnUserService;
use App\Http\Controllers\Controller;
use App\Pipe\App\PipeAppRewardAchieve;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Throwable;

class RewardController extends Controller
{
    /**
     * @throws Exception
     */
    public function invoke(Request $request): Response
    {
        return $this->render('Reward/Index', [

            // 報酬情報.
            'paginationReward' => fn () => $this->getPaginationReward($request, [
                'MstReward',
            ]),
        ]);
    }

    public function list(Request $request): Response
    {
        return $this->render('Reward/List/Index', [
            // 報酬マスタ情報.
            'paginationReward' => fn () => $this->getPaginationMstReward($request, []),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function achieve(Request $request): RedirectResponse
    {
        // パイプ作成.
        PipeService::insertNewPipe(
            new PipeAppRewardAchieve($request->toArray())
        );

        // バリデーションチェック.
        if (! PipeService::isValid()) {
            return redirect()->action(EPages::REWARD->getInvokePath());
        }

        // 更新処理.
        DB::transaction(static function (): void {
            // 獲得処理.
            TrnUserService::updateByPipe();

            // 報酬フラグの更新.
            TrnUserRewardService::updateByPipe();
        });

        return redirect()->action(EPages::REWARD->getInvokePath());
    }

    /**
     * ページネーション・報酬.
     *
     * @param  array<mixed>  $with
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    private function getPaginationReward(Request $request, array $with = []): array
    {
        // パラメータ受け取り.
        $pageIndex        = (int) $request->input('index', 0);
        $pageStep         = (int) $request->input('step', 20);

        return TrnUserRewardService::offsetPagination(
            $this->authUser?->TrnUser?->id ?: 0,
            $pageIndex,
            $pageStep,
            $with
        );
    }

    /**
     * ページネーション・報酬マスタ.
     *
     * @param  array<mixed>  $with
     * @return array<string, mixed>
     */
    private function getPaginationMstReward(Request $request, array $with = []): array
    {
        // パラメータ受け取り.
        $pageIndex        = (int) $request->input('index', 0);
        $pageStep         = (int) $request->input('step', 20);

        return MstRewardService::offsetPagination(
            $pageIndex,
            $pageStep,
            $with
        );
    }
}

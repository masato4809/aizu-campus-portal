<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Enum\App\EPages;
use App\Facades\Internal\PipeService;
use App\Facades\Models\App\Trn\TrnGoodJobService;
use App\Http\Controllers\Controller;
use App\Pipe\App\PipeAppGoodJobCreate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;

class GoodJobController extends Controller
{
    /**
     * 感謝を記録する基本ページ表示処理.
     */
    public function invoke(Request $request): Response
    {
        return $this->render('GoodJob/Index', [

            // GoodJob情報.
            'paginationGoodJob' => fn () => $this->getPaginationGoodJob($request, [
                'FromTrnUser',
                'FromTrnDivision',
                'FromTrnProject',

                'ToTrnUser',
                'ToTrnDivision',
                'ToTrnProject',
            ]),
        ]);
    }

    /**
     * 感謝を記録する・新規作成.
     */
    public function new(Request $request): Response
    {
        return $this->render('GoodJob/New/Index', [
            // セッションにバリデーション情報があれば取得する.
            'serverValidation' => fn () => session('serverValidation') ?? null,
        ]);
    }

    /**
     * 感謝を記録する・登録処理.
     *
     * @throws \Throwable
     */
    public function create(Request $request): RedirectResponse
    {
        // パイプ作成.
        PipeService::insertNewPipe(
            new PipeAppGoodJobCreate($request->toArray())
        );

        // バリデーションチェック.
        if (! PipeService::isValid()) {
            return redirect()->action(EPages::GOOD_JOB__NEW->getInvokePath())
                ->with([
                    'serverValidation' => PipeService::getValidationResult(),
                ]);
        }

        // 更新処理.
        DB::transaction(static function (): void {
            // GoodJob情報の更新.
            TrnGoodJobService::updateByPipe();
        });

        return redirect()->action(EPages::GOOD_JOB->getInvokePath());
    }

    /**
     * ページネーション・GoodJob.
     *
     * @param  array<mixed>  $with
     * @return array<string, mixed>
     */
    private function getPaginationGoodJob(Request $request, array $with = []): array
    {
        // パラメータ受け取り.
        $pageIndex = (int) $request->input('index', 0);
        $pageStep  = (int) $request->input('step', 20);

        return TrnGoodJobService::offsetPagination(
            $pageIndex,
            $pageStep,
            $with
        );
    }
}

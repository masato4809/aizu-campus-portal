<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Enum\App\EPages;
use App\Facades\Internal\PipeService;
use App\Facades\Models\App\Trn\TrnDivisionService;
use App\Facades\Models\App\Trn\TrnDivisionUserService;
use App\Http\Controllers\Controller;
use App\Pipe\App\PipeAppDivisionCreate;
use App\Pipe\App\PipeAppDivisionDelete;
use App\Pipe\App\PipeAppDivisionUpdate;
use App\Pipe\App\PipeAppDivisionUserUpdate;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Throwable;

class DivisionController extends Controller
{
    /**
     * @throws Exception
     */
    public function invoke(Request $request): Response
    {
        if (! Auth::check()) {
            throw new Exception;
        }

        return $this->render('Division/Index', [

            // 課情報.
            'paginationDivision' => fn () => $this->getPaginationDivision($request, [
                'TrnDivisionUser',
                'TrnDivisionUser' => [
                    'TrnUser',
                ],
            ]),
        ]);
    }

    /**
     * @throws Exception
     */
    public function show(Request $request): Response
    {
        if (! Auth::check()) {
            throw new Exception;
        }

        return $this->render('Division/Show/Index', [

            // 課情報.
            'trnDivision' => fn () => $this->getDivision($request, [
                'TrnDivisionUser',
                'TrnDivisionUser' => [
                    'TrnUser',
                ],
            ]),
        ]);
    }

    /**
     * @throws Exception
     */
    public function edit(Request $request): Response
    {
        if (! Auth::check()) {
            throw new Exception;
        }

        return $this->render('Division/Edit/Index', [

            // 課情報.
            'trnDivision'      => fn () => $this->getDivision($request, [
                'TrnDivisionUser',
                'TrnDivisionUser' => [
                    'TrnUser',
                ],
            ]),

            // セッションにバリデーション情報があれば取得する.
            'serverValidation' => fn () => session('serverValidation') ?? null,
        ]);
    }

    /**
     * 課の更新.
     *
     * @throws Throwable
     */
    public function update(Request $request): RedirectResponse
    {
        // パイプ作成.
        /** @var PipeAppDivisionUpdate $pipe */
        $pipe = PipeService::insertNewPipe(
            new PipeAppDivisionUpdate($request->toArray())
        );

        // バリデーションチェック.
        if (! PipeService::isValid()) {
            return redirect()->action(EPages::DIVISION__EDIT->getInvokePath(), [
                'divisionId' => $pipe->trnDivisionId,
            ])->with([
                'serverValidation' => PipeService::getValidationResult(),
            ]);
        }

        // 更新処理.
        DB::transaction(static function (): void {
            // 課情報の更新.
            TrnDivisionService::updateByPipe();
        });

        return redirect()->action(EPages::DIVISION__SHOW->getInvokePath(), [
            'divisionId' => $pipe->trnDivisionId,
        ]);
    }

    public function new(Request $request): Response
    {
        return $this->render('Division/New/Index', [
            // セッションにバリデーション情報があれば取得する.
            'serverValidation' => fn () => session('serverValidation') ?? null,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function create(Request $request): RedirectResponse
    {
        // パイプ作成.
        PipeService::insertNewPipe(
            new PipeAppDivisionCreate($request->toArray())
        );

        // バリデーションチェック.
        if (! PipeService::isValid()) {
            return redirect()->action(EPages::DIVISION__NEW->getInvokePath())
                ->with([
                    'serverValidation' => PipeService::getValidationResult(),
                ]);
        }
        // 更新処理.
        DB::transaction(static function (): void {
            // ユーザー情報の更新.
            TrnDivisionService::updateByPipe();
        });

        return redirect()->action(EPages::DIVISION->getInvokePath());
    }

    /**
     * 課メンバーの更新.
     *
     * @throws Throwable
     */
    public function update_user(Request $request): RedirectResponse
    {
        // パイプ作成.
        /** @var PipeAppDivisionUserUpdate $pipe */
        $pipe = PipeService::insertNewPipe(
            new PipeAppDivisionUserUpdate($request->toArray())
        );

        // バリデーションチェック.
        if (! PipeService::isValid()) {
            return redirect()->action(EPages::DIVISION__SHOW->getInvokePath(), [
                'divisionId' => $pipe->trnDivisionId,
            ]);
        }

        // 更新処理.
        DB::transaction(static function (): void {
            // 所属メンバーの更新.
            TrnDivisionUserService::updateByPipe();
        });

        return redirect()->action(EPages::DIVISION__SHOW->getInvokePath(), [
            'divisionId' => $pipe->trnDivisionId,
        ]);
    }

    /**
     * ページネーション・課
     *
     * @param  array<mixed>  $with
     * @return array<mixed>
     */
    private function getPaginationDivision(Request $request, array $with = []): array
    {
        // パラメータ受け取り.
        $pageIndex            = (int) $request->input('index', 0);
        $pageStep             = (int) $request->input('step', 5);

        return TrnDivisionService::offsetPagination(
            $pageIndex,
            $pageStep,
            $with
        );
    }

    /**
     * 単独取得・課.
     *
     * @param  array<mixed>  $with
     * @return array<mixed>
     */
    private function getDivision(Request $request, array $with = []): array
    {
        return TrnDivisionService::findByIdOrFail(
            (int) $request->route('divisionId'),
            $with,
        )->toPayload($with);
    }

    /**
     * 課の削除処理
     */
    public function delete(Request $request): RedirectResponse
    {
        // パイプ作成.
        PipeService::insertNewPipe(
            new PipeAppDivisionDelete($request->toArray())
        );

        // 更新処理.
        DB::transaction(static function (): void {
            // 課の削除.
            TrnDivisionService::updateByPipe();
        });

        return redirect()->action(EPages::DIVISION->getInvokePath());
    }
}

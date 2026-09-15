<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Enum\App\EPages;
use App\Facades\Internal\PipeService;
use App\Facades\Models\App\Trn\TrnUserRewardService;
use App\Facades\Models\App\Trn\TrnUserService;
use App\Facades\Models\App\Trn\TrnUserSlackProfileService;
use App\Http\Controllers\Controller;
use App\Pipe\App\PipeAppPersonalSettingEditUpdate;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Throwable;

class PersonalSettingController extends Controller
{
    /**
     * 個人設定・閲覧.
     *
     * @throws Exception
     */
    public function show(): Response
    {
        if (! Auth::check()) {
            throw new Exception;
        }

        return $this->render('PersonalSetting/Show/Index', [
            'trnUserSlackProfile' => fn () => $this->getTrnUserSlackProfile(),
        ]);
    }

    /**
     * 個人設定・編集.
     *
     * @throws Exception
     */
    public function edit(): Response
    {
        if (! Auth::check()) {
            throw new Exception;
        }

        return $this->render('PersonalSetting/Edit/Index', [
            'trnUserSlackProfile' => fn () => $this->getTrnUserSlackProfile(),

            // セッションにバリデーション情報があれば取得する.
            'serverValidation'    => fn () => session('serverValidation') ?? null,
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
        PipeService::insertNewPipe(
            new PipeAppPersonalSettingEditUpdate($request->toArray())
        );

        // バリデーションチェック.
        if (! PipeService::isValid()) {
            // エラーがある場合は入力ページにリダイレクトする.
            // バリデーション情報はセッションに保存.
            return redirect()->action(EPages::PERSONAL_SETTING__EDIT->getInvokePath())
                ->with([
                    'serverValidation' => PipeService::getValidationResult(),
                ]);
        }

        // 更新処理.
        DB::transaction(static function (): void {
            // 個人設定の更新.
            TrnUserService::updateByPipe();

            // Slack情報の更新.
            TrnUserSlackProfileService::updateByPipe();

            // 報酬情報の更新.
            TrnUserRewardService::updateByPipe();
        });

        return redirect()->action(EPages::PERSONAL_SETTING__SHOW->getInvokePath());
    }

    /**
     * TrnUserSlackProfileの取得.
     *
     * @return array<mixed>
     */
    private function getTrnUserSlackProfile(): array
    {
        $authId  = (int) Auth::id();

        // ユーザー情報を取得.
        $trnUser = TrnUserService::findByAuthId($authId);
        if ($trnUser === null) {
            return [];
        }

        return $trnUser->TrnUserSlackProfile?->toPayload() ?? [];
    }
}

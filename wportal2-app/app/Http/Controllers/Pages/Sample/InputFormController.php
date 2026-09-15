<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages\Sample;

use App\Enum\App\EPages;
use App\Facades\Internal\PipeService;
use App\Http\Controllers\Controller;
use App\Pipe\Sample\PipeSampleInputFormUpdate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class InputFormController extends Controller
{
    public function invoke(Request $request): Response
    {
        return $this->render('Sample/InputForm/Index', [
            // セッションにバリデーション情報があれば取得する.
            'serverValidation' => fn () => session('serverValidation') ?? null,
        ]);
    }

    /**
     * Post通信によるcreate.
     */
    public function create(Request $request): Response|RedirectResponse
    {
        // パイプ作成.
        PipeService::insertNewPipe(
            new PipeSampleInputFormUpdate($request->toArray())
        );

        // バリデーションチェック.
        if (! PipeService::isValid()) {
            // エラーがある場合は入力ページにリダイレクトする.
            // バリデーション情報はセッションに保存.
            return redirect()->action(EPages::SAMPLE_INPUT_FORM->getInvokePath())
                ->with([
                    'serverValidation' => PipeService::getValidationResult(),
                ]);
        }

        // NOTE: 更新処理はここで実施する想定.

        // 確認画面に移動.
        return $this->render('Sample/InputForm/Create/Index');
    }
}

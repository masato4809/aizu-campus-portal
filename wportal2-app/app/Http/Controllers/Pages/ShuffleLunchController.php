<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Enum\App\EPages;
use App\Enum\App\ShuffleLunch\ETimeZone;
use App\Facades\External\GoogleCalendarService;
use App\Facades\External\SlackService;
use App\Facades\Internal\PipeService;
use App\Facades\Models\App\Trn\TrnShuffleLunchEntryService;
use App\Facades\Models\App\Trn\TrnShuffleLunchGroupService;
use App\Facades\Usecases\UsecasesShuffleLunchService;
use App\Http\Controllers\Controller;
use App\Models\App\Trn\TrnShuffleLunchEntry;
use App\Models\App\Trn\TrnShuffleLunchGroup;
use App\Pipe\App\PipeAppShuffleLunchCancel;
use App\Pipe\App\PipeAppShuffleLunchRakumo;
use App\Pipe\App\PipeAppShuffleLunchRegister;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Throwable;

class ShuffleLunchController extends Controller
{
    /**
     * シャッフルランチ基本ページ表示処理.
     */
    public function invoke(): Response
    {
        // マッチ済みのtimezone&calculateが未計算なら計算処理を実施する.
        $timezone  = UsecasesShuffleLunchService::getTodayTimeZone();
        $calculate = UsecasesShuffleLunchService::getTodayCalculate();
        if ($timezone === ETimeZone::MATCHED && $calculate->isStandBy()) {
            UsecasesShuffleLunchService::execMatching();
        }

        return $this->render('ShuffleLunch/Index', [

            // マッチング情報.
            'timezone'                 => fn () => UsecasesShuffleLunchService::getTodayTimeZone(),
            'calculate'                => fn () => UsecasesShuffleLunchService::getTodayCalculate(),

            // 本日のエントリー.
            'trnShuffleLunchEntryList' => fn () => $this->getTodayEntryList(),

            // 本日のグループ.
            'trnShuffleLunchGroupList' => fn () => $this->getTodayGroupList([
                'TrnUser',
            ]),

            // エラーメッセージ.
            'message'                  => fn () => session('message') ?? null,
        ]);
    }

    /**
     * シャッフルランチへの参加表明.
     *
     * @throws Throwable
     */
    public function register(Request $request): RedirectResponse
    {
        // 計算済み状態ならば処理不可.
        $todayCalculate = UsecasesShuffleLunchService::getTodayCalculate();
        if (! $todayCalculate->isStandBy()) {
            $time = Carbon::now()->format('[H:i:s]');

            return redirect()->action(EPages::SHUFFLE_LUNCH->getInvokePath())
                ->with([
                    'message' => "{$time}本日のシャッフルランチは既にマッチング済みのため処理できませんでした",
                ]);
        }

        // パイプ作成.
        PipeService::insertNewPipe(
            new PipeAppShuffleLunchRegister($request->toArray())
        );

        // バリデーションチェック.
        if (! PipeService::isValid()) {
            return redirect()->action(EPages::SHUFFLE_LUNCH->getInvokePath());
        }

        // 更新処理.
        DB::transaction(static function (): void {
            // エントリ情報の更新.
            TrnShuffleLunchEntryService::updateByPipe();

            SlackService::updateByPipe();
        });

        return redirect()->action(EPages::SHUFFLE_LUNCH->getInvokePath());
    }

    /**
     * シャッフルランチのキャンセル.
     *
     * @throws Throwable
     */
    public function cancel(Request $request): RedirectResponse
    {
        // 計算済み状態ならば処理不可.
        $todayCalculate = UsecasesShuffleLunchService::getTodayCalculate();
        if (! $todayCalculate->isStandBy()) {
            $time = Carbon::now()->format('[H:i:s]');

            return redirect()->action(EPages::SHUFFLE_LUNCH->getInvokePath())
                ->with([
                    'message' => "{$time}本日のシャッフルランチは既にマッチング済みのため処理できませんでした",
                ]);
        }

        // パイプ作成.
        PipeService::insertNewPipe(
            new PipeAppShuffleLunchCancel($request->toArray())
        );

        // バリデーションチェック.
        if (! PipeService::isValid()) {
            return redirect()->action(EPages::SHUFFLE_LUNCH->getInvokePath());
        }

        // 更新処理.
        DB::transaction(static function (): void {
            // エントリ情報の更新.
            TrnShuffleLunchEntryService::updateByPipe();
        });

        return redirect()->action(EPages::SHUFFLE_LUNCH->getInvokePath());
    }

    /**
     * シャッフルランチのキャンセル.
     *
     * @throws Throwable
     */
    public function rakumo(Request $request): RedirectResponse
    {
        // 計算済前ならば処理不可.
        $todayCalculate = UsecasesShuffleLunchService::getTodayCalculate();
        if ($todayCalculate->isStandBy()) {
            $time = Carbon::now()->format('[H:i:s]');

            return redirect()->action(EPages::SHUFFLE_LUNCH->getInvokePath())
                ->with([
                    'message' => "{$time}マッチング前のため実施できません",
                ]);
        }

        // パイプ作成.
        /** @var PipeAppShuffleLunchRakumo $pipe */
        $pipe           = PipeService::insertNewPipe(
            new PipeAppShuffleLunchRakumo($request->toArray())
        );

        // バリデーションチェック.
        if (! PipeService::isValid()) {
            return redirect()->action(EPages::SHUFFLE_LUNCH->getInvokePath());
        }

        // 操作者が919.jpでない場合.
        if (! str_contains($pipe->authUser?->email ?: '', '@919.jp')) {
            $time = Carbon::now()->format('[H:i:s]');

            return redirect()->action(EPages::SHUFFLE_LUNCH->getInvokePath())
                ->with([
                    'message' => "{$time}カレンダー操作権限がありません",
                ]);
        }

        // 更新処理.
        DB::transaction(static function () use ($pipe): void {
            // rakumoのブロック実行.
            if (! $pipe->isBlocked) {
                GoogleCalendarService::blockSchedule(
                    $pipe->authUser?->email,
                    $pipe->emailList,
                    $pipe->start,
                    $pipe->end,
                    $pipe->summary,
                    '※ここは直接指定できるようにします',
                    '※ここは直接指定できるようにします'
                );
            }

            // groupのblockedフラグ設定.
            TrnShuffleLunchGroupService::updateByPipe();
        });

        $time           = Carbon::now()->format('[H:i:s]');

        return redirect()->action(EPages::SHUFFLE_LUNCH->getInvokePath())
            ->with([
                'message' => "{$time}rakumoのブロックが完了しました",
            ]);
    }

    /**
     * 今日のエントリーの取得
     *
     * @param  array<mixed>  $with
     * @return array<mixed>
     */
    private function getTodayEntryList(array $with = []): array
    {
        // 必要なのは準備中の時のみ.
        if (! UsecasesShuffleLunchService::getTodayCalculate()->isStandBy()) {
            return [];
        }

        return TrnShuffleLunchEntryService::listToday()
            ->map(function (TrnShuffleLunchEntry $trnShuffleLunchEntry) use ($with) {
                return $trnShuffleLunchEntry->toPayload($with);
            })->toArray();
    }

    /**
     * 今日の成立済グループの取得
     *
     * @param  array<mixed>  $with
     * @return array<mixed>
     */
    private function getTodayGroupList(array $with = []): array
    {
        // 必要なのは成立後のみ.
        if (UsecasesShuffleLunchService::getTodayCalculate()->isStandBy()) {
            return [];
        }

        return TrnShuffleLunchGroupService::listToday($with)
            ->map(function (TrnShuffleLunchGroup $trnShuffleLunchGroup) use ($with) {
                return $trnShuffleLunchGroup->toPayload($with);
            })->toArray();
    }
}

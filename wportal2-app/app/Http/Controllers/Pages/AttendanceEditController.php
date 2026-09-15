<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Enum\App\EPages;
use App\Facades\Internal\PipeService;
use App\Facades\Models\App\Trn\TrnAttendanceStateService;
use App\Facades\Models\App\Trn\TrnUserService;
use App\Http\Controllers\Controller;
use App\Models\App\Trn\TrnAttendanceState;
use App\Pipe\App\PipeAppAttendanceEditUpdate;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Throwable;

class AttendanceEditController extends Controller
{
    /**
     * 勤怠修正.
     */
    public function invoke(Request $request): Response
    {
        // 新しく表示する指定月、なければ今月とする.
        $dateTimeString = $this->parseDateTime($request);

        // 取得範囲の作成
        $startOfMonth   = Carbon::createFromTimeString($dateTimeString)->startOfMonth();
        $endOfMonth     = Carbon::createFromTimeString($dateTimeString)->endOfMonth();

        // 取得対象.
        $trnUser        = TrnUserService::findByAuthIdOrFail((int) Auth::id());

        return $this->render('AttendanceEdit/Index', [
            'dateTime'               => fn () => $dateTimeString,

            // 出退勤状態の取得.
            'trnAttendanceStateList' => fn () => TrnAttendanceStateService::listByUserIdAndPeriod(
                $trnUser->id,
                $startOfMonth,
                $endOfMonth
            )->map(fn (TrnAttendanceState $trnAttendanceState) => $trnAttendanceState->toPayload()),
        ]);
    }

    /**
     * 勤怠修正・更新.
     *
     * @throws Throwable
     */
    public function update(Request $request): RedirectResponse
    {
        // パイプ作成.
        PipeService::insertNewPipe(
            new PipeAppAttendanceEditUpdate($request->toArray())
        );

        // 更新処理.
        DB::transaction(static function (): void {
            // 勤怠時間の更新.
            TrnAttendanceStateService::updateByPipe();
        });

        // 新しく表示する指定月、なければ今月とする.
        $dateTimeString = $this->parseDateTime($request);

        return redirect()->action(EPages::ATTENDANCE__EDIT->getInvokePath(), [
            'dateTime' => $dateTimeString,
        ]);
    }

    /**
     * 新しく表示する指定月をリクエストから取得、なければ今月とする.
     */
    private function parseDateTime(Request $request): string
    {
        return $request->has('dateTime')
            ? (string) $request->input('dateTime')
            : Carbon::now()->toString();
    }
}

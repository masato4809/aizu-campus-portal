<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Facades\External\SlackService;
use App\Facades\Internal\PipeService;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use PDOException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     **/
    protected $dontReport      = [];

    /**
     * @var array<string>
     */
    protected array $dontSlack = [
        '',
    ];

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var list<string>
     */
    protected $dontFlash       = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * エラーチャンネルへの通知処理.
     *
     * @throws Throwable
     */
    public function report(Throwable $e): void
    {
        // レポート除外の場合は処理しない.
        if ($this->shouldntReport($e)) {
            return;
        }

        // Slack通知を行わないExceptionは通知のみ.
        if ($this->shouldntSlack($e)) {
            $this->reportLog($e);

            return;
        }

        // 通知用のuniqueId.
        $uid                   = uniqid();

        // 個人情報フィルタが必要なExceptionはSlackに送信しない.
        $message               = $this->isRequirePrivacyFilter($e)
            ? implode(',', $e->errorInfo ?? [])
            : $e->getMessage();

        // Slackへの通知.
        $filePath              = str_replace(app_path(), '', $e->getFile());
        $line                  = $e->getLine();
        SlackService::notifyError(
            $message,
            __CLASS__,
            "[$uid]:$filePath:$line"
        );

        // 個人情報フィルタの有無に関わらずログをkibanaに流す.
        Log::error("[$uid]:$filePath:$line:".$e->getMessage());

        // 発生時のスタックTraceはkibanaに流す.
        Log::error("[$uid]:$filePath:$line:".$e->getTraceAsString());

        // 発生時のパラメータをkibanaに流す.
        [$pipeKind, $pipeArgs] = PipeService::getPipeArgs();
        Log::error("[$uid]:param:".print_r(PipeService::getParamArgs(), true));
        Log::error("[$uid]:pipeKind($pipeKind->value):".print_r($pipeArgs, true));
    }

    /**
     * ログ出力のみの通知処理.
     *
     * @throws Throwable
     */
    public function reportLog(Throwable $e): void
    {
        // 通知用のuniqueId.
        $uid                   = uniqid();
        $filePath              = str_replace(app_path(), '', $e->getFile());
        $line                  = $e->getLine();

        // 個人情報フィルタの有無に関わらずログをkibanaに流す.
        Log::error("[$uid]:$filePath:$line:".$e->getMessage());

        // 発生時のスタックTraceはkibanaに流す.
        Log::error("[$uid]:$filePath:$line:".$e->getTraceAsString());

        // 発生時のパラメータをkibanaに流す.
        [$pipeKind, $pipeArgs] = PipeService::getPipeArgs();
        Log::error("[$uid]:param:".print_r(PipeService::getParamArgs(), true));
        Log::error("[$uid]:pipeKind($pipeKind->value):".print_r($pipeArgs, true));
    }

    /**
     * 個人情報保護用のフィルタが必要かどうか.
     */
    private function isRequirePrivacyFilter(Throwable $e): bool
    {
        return $e instanceof PDOException;
    }

    /**
     * Slack通知を行うかどうか.
     */
    protected function shouldntSlack(Throwable $e): bool
    {
        $dontSlack = array_merge($this->dontSlack);

        return Arr::first($dontSlack, fn ($type) => $e instanceof $type) !== null;
    }
}

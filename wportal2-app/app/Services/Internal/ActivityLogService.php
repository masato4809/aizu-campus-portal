<?php

declare(strict_types=1);

namespace App\Services\Internal;

use App\Models\App\Auth\AuthUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Class ActivityLogService
 */
final class ActivityLogService
{
    /** @var Collection<int, string>|null */
    private ?Collection $readyLogList       = null;

    /** @var Collection<int, string>|null */
    private ?Collection $transactionLogList = null;

    /**
     * Modelの変更を実施.
     *
     * @param  array<mixed>  $appendKeyValue
     */
    public function changeModel(
        ?AuthUser $authUser,
        string $className,
        string $functionName,
        string $line,
        Model $model,
        array $appendKeyValue
    ): void {
        $id     = (string) $model->getKey();
        $authId = $authUser ? $authUser->id : 0;
        $output = '[changeDB] ';
        $output .= "Auth:$authId ";
        $output .= "$className:$functionName:$line ";
        $output .= "Id:$id";

        // Modelのdirty確認.
        foreach ($model->getDirty() as $key => $value) {
            $value    = (string) $this->filterString($value);
            $original = (string) $this->filterString($model->getOriginal($key));

            $output .= "$key:$original => $value ";
        }

        // 追加項目.
        foreach ($appendKeyValue as $key => $value) {
            $value = (string) $this->filterString($value);

            $output .= "$key:$value ";
        }

        // ログを保持.
        $this->storeLog($output);
    }

    /**
     * 発行未確定のログ集積を開始.
     */
    public function beginTransaction(): void
    {
        // トランザクションログを準備.
        $this->transactionLogList = collect();
    }

    /**
     * 発行未確定のログ出力を確定.
     *
     * @throws \Exception
     */
    public function commitTransaction(): void
    {
        // トランザクション中でなければ処理なし.
        if ($this->transactionLogList === null || $this->readyLogList === null) {
            return;
        }

        // 準備ログにトランザクションログを結合する.
        $this->readyLogList       = $this->readyLogList->merge($this->transactionLogList);
        $this->transactionLogList = null;
    }

    /**
     * 発行未確定のログを削除.
     */
    public function rollbackTransaction(): void
    {
        // トランザクションログを削除.
        $this->transactionLogList = null;
    }

    /**
     * ログの出力.
     */
    public function outputLog(): void
    {
        if ($this->readyLogList === null) {
            return;
        }

        // ログを全て出力.
        $this->readyLogList->each(function (string $log) {
            Log::info($log);
        });
    }

    /**
     * 改行コードなど、ログに反映させたくないものをフィルタ.
     */
    private function filterString(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        return (string) str_replace("\n", '\\n', $value);
    }

    /**
     * ログを出力準備に保持.
     */
    private function storeLog(string $log): void
    {
        // 準備ログが存在しなければ作成しておく.
        if ($this->readyLogList === null) {
            $this->readyLogList = collect();
        }

        // トランザクション中はトランザクションに格納するのみ.
        if ($this->transactionLogList !== null) {
            $this->transactionLogList->add($log);

            return;
        }

        $this->readyLogList->add($log);
    }
}

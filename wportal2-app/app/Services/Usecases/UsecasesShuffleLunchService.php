<?php

declare(strict_types=1);

namespace App\Services\Usecases;

use App\Enum\App\ShuffleLunch\ECalculate;
use App\Enum\App\ShuffleLunch\ETimeZone;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

class UsecasesShuffleLunchService
{
    public const TTL = 3600 * 24;

    /**
     * 現在のタイムゾーンを取得する
     *
     * @note 11時にマッチング開始となる.
     */
    public function getTodayTimeZone(): ETimeZone
    {
        $carbon = Carbon::now();
        if ($carbon->hour >= 11) {
            return ETimeZone::MATCHED;
        }

        return ETimeZone::STANDBY;
    }

    /**
     * 本日のシャッフルランチの計算状況を取得する.
     */
    public function getTodayCalculate(): ECalculate
    {
        $key   = $this->getRedisKey();

        // キー登録がなければ準備状態.
        if (! Cache::has($key)) {
            return ECalculate::READY;
        }

        // キャッシュから現在の状態を取得.
        $value = (int) Cache::get($key, ECalculate::READY->value);

        return ECalculate::tryFrom($value) ?? ECalculate::READY;
    }

    /**
     * 現時点のRedisキーを取得する.
     */
    public function getRedisKey(): string
    {
        $dateString = Carbon::now()->format('Ymd');

        return "{$dateString}_shuffle_lunch_calculate";
    }

    /**
     * Redisの保存時間を取得.
     */
    public function getRedisTTL(): int
    {
        return self::TTL;
    }

    /**
     * マッチング処理の実施.
     *
     * @throws InvalidArgumentException
     */
    public function execMatching(): void
    {
        // 計算中フラグを設定.
        Cache::set(
            $this->getRedisKey(),
            ECalculate::CALCULATING->value,
            $this->getRedisTTL()
        );

        // artisanコマンドを呼び出す.
        Artisan::call('batch:ShuffleLunchMatching');
    }
}

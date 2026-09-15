<?php

declare(strict_types=1);

namespace App\Enum\App\ShuffleLunch;

enum ECalculate: int
{
    case INVALID            = 0; // 無効値.

    case READY              = 1; // 計算未処理.
    case CALCULATING        = 2; // 計算中.
    case ESTABLISHED        = 3; // 計算済.

    /**
     * 待機中(マッチング登録可能)かどうか.
     */
    public function isStandBy(): bool
    {
        return match ($this) {
            self::READY => true,
            default     => false,
        };
    }
}

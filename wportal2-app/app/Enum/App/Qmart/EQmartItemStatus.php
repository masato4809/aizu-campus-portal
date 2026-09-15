<?php

declare(strict_types=1);

namespace App\Enum\App\Qmart;

/**
 * Qマート出品ステータス.
 *
 * @note 値の変更禁止.
 */
enum EQmartItemStatus: int
{
    case SELLING      = 1; // 出品中.
    case NEGOTIATING  = 2; // 交渉中.
    case SOLD         = 3; // 売却済.
    case ARCHIVED     = 4; // アーカイブ.

    /**
     * ステータスに対応したラベルを取得する.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::SELLING     => '出品中',
            self::NEGOTIATING => '交渉中',
            self::SOLD        => '売却済',
            self::ARCHIVED    => 'アーカイブ',
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Enum\App;

/**
 * 商品カテゴリ.
 */
enum EGoodsCategory: int
{
    case INVALID      = 0; // 指定なし.
    case LUNCH_TICKET = 1; // ランチチケット.
}

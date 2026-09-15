<?php

declare(strict_types=1);

namespace App\Enum\Mst;

/**
 * 商品の種別.
 */
enum EMstGoods: int
{
    case INVALID                    = 0; // 指定なし.
    case LUNCH_TICKET_TOBARU_LOW    = 1; // ランチチケット・桃原LOW.
    case LUNCH_TICKET_YAMADA_LOW    = 2; // ランチチケット・山田LOW.
}

<?php

declare(strict_types=1);

namespace App\Enum\App;

/**
 * 許可フラグ.
 */
enum EEnableFlag: int
{
    case INVALID = 0; // 指定なし・不許可.
    case ENABLE  = 1; // 許可.
}

<?php

declare(strict_types=1);

namespace App\Enum\App\ShuffleLunch;

enum EGroupType: int
{
    case INVALID  = 0; // 無効値.

    case THREE    = 3; // 3人グループ.
    case FOUR     = 4; // 4人グループ.
    case FIVE     = 5; // 5人グループ.
}

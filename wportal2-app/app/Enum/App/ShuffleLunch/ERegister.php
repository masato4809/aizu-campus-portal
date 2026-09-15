<?php

declare(strict_types=1);

namespace App\Enum\App\ShuffleLunch;

enum ERegister: int
{
    case INVALID      = 0; // 無効値.

    case REGISTERED   = 1; // 登録済み.
    case UNREGISTERED = 2; // 未登録.
}

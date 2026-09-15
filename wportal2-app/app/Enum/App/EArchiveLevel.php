<?php

declare(strict_types=1);

namespace App\Enum\App;

enum EArchiveLevel: int
{
    case INVALID = 0; // 無効値.

    case ALIVE   = 1; // 生存.
    case ARCHIVE = 2; // アーカイブ.
    case DELETE  = 3; // 削除設定.
}

<?php

declare(strict_types=1);

namespace App\Enum\App\GoodJob;

enum ETargetType: int
{
    case INVALID  = 0; // 無効値.

    case USER     = 1; // ユーザー.
    case DIVISION = 2; // 部署.
    case PROJECT  = 3; // プロジェクト.
    case LABEL    = 4; // ラベル直指定.
}

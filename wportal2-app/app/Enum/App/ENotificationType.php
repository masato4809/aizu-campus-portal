<?php

declare(strict_types=1);

namespace App\Enum\App;

/**
 * 通知タイプ.
 *
 * @note 値の変更禁止.
 */
enum ENotificationType: int
{
    case INVALID       = 0; // 無効.

    case SLACK_CHANNEL = 1; // Slack通知.
}

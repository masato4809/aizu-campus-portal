<?php

declare(strict_types=1);

namespace App\Enum\Mst;

/**
 * 報酬の種別.
 */
enum EMstReward: int
{
    case INVALID                   = 0; // 指定なし.
    case CREATE_ACCOUNT            = 1; // 華麗なる登場.
    case UPDATE_PROFILE            = 2; // 前略プロフィール.
    case CREATE_PERFECT_ATTENDANCE = 3; // 紳士の勤怠.
    case GOOD_JOB                  = 4; // ささやかながらの花束を.
}

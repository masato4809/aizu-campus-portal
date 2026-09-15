<?php

declare(strict_types=1);

namespace App\Enum\App\ShuffleLunch;

use Carbon\Carbon;
use Exception;

enum EEventTimeZone: int
{
    case INVALID = 0; // 無効値.

    case EARLY   = 1; // 11:30-12:30.
    case NORMAL  = 2; // 12:00-13:00.

    /**
     * 開始時間を取得.
     *
     * @throws Exception
     */
    public function getStart(): Carbon
    {
        return match ($this) {
            self::EARLY  => Carbon::createFromTime(11, 30),
            self::NORMAL => Carbon::createFromTime(12, 0),
            default      => throw new Exception('Invalid value'),
        };
    }

    /**
     * 終了時間を取得.
     *
     * @throws Exception
     */
    public function getEnd(): Carbon
    {
        return match ($this) {
            self::EARLY  => Carbon::createFromTime(12, 30),
            self::NORMAL => Carbon::createFromTime(13, 0),
            default      => throw new Exception('Invalid value'),
        };
    }
}

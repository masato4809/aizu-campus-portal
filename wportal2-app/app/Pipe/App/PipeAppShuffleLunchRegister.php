<?php

declare(strict_types=1);

namespace App\Pipe\App;

use App\Enum\App\EPipeKind;
use App\Enum\App\ShuffleLunch\EEventTimeZone;
use App\Models\App\Auth\AuthUser;
use App\Pipe\PipeBase;
use Illuminate\Support\Facades\Auth;

final class PipeAppShuffleLunchRegister extends PipeBase
{
    public EEventTimeZone $eventTimeZone = EEventTimeZone::INVALID;

    public ?AuthUser $authUser           = null;

    public int $entryCount               = 0;

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::APP_SHUFFLE_LUNCH_REGISTER, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        $this->eventTimeZone = EEventTimeZone::tryFrom(
            $this->getArgAsInteger('zone')
        ) ?? EEventTimeZone::INVALID;

        $this->authUser      = Auth::user();
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void {}
}

<?php

declare(strict_types=1);

namespace App\Pipe\App;

use App\Enum\App\EPipeKind;
use App\Pipe\App\Input\InputEvent;
use App\Pipe\PipeBase;
use Illuminate\Support\Collection;

final class PipeAppAttendanceEditUpdate extends PipeBase
{
    /** @var Collection<int, InputEvent> */
    public Collection $eventList;

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        $this->eventList = collect();
        parent::__construct(EPipeKind::APP_ATTENDANCE_EDIT_UPDATE, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        // 修正時間のリストを取得.
        $eventList = (array) $this->args['eventList'];
        foreach ($eventList as $event) {
            /** @var array<mixed> $event */
            $this->eventList->add(InputEvent::createFromArray($event));
        }
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void {}
}

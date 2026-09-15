<?php

declare(strict_types=1);

namespace App\Services\External;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Google\Service\Calendar\Event;
use Illuminate\Support\Collection;

interface GoogleCalendarServiceInterface
{
    /**
     * @return Collection<int, Event>
     */
    public function listSchedule(string $calendarId, CarbonImmutable $startDate, CarbonImmutable $endDate): Collection;

    /**
     * @param  Collection<int, string>  $calendarIdList
     * @param  string|null  $transparency  'opaque'（予定あり）または 'transparent'（予定なし）。null の場合は API デフォルトに委ねる
     * @param  bool  $allDay  true の場合は Google Calendar の終日イベント（date フィールド）として登録する
     */
    public function blockSchedule(
        string $authEmail,
        Collection $calendarIdList,
        Carbon $start,
        Carbon $end,
        string $summary,
        string $location = '',
        string $description = '',
        ?string $descriptionPrefix = null,
        ?string $transparency = null,
        bool $allDay = false,
        ?string $eventType = null,
        ?string $source = null,
    ): void;

    /**
     * @param  callable(Event):bool|null  $filter
     */
    public function deleteSchedules(string $calendarId, CarbonImmutable $startDate, CarbonImmutable $endDate, ?callable $filter = null): void;
}

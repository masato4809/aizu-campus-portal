<?php

declare(strict_types=1);

namespace App\Services\External;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Google\Exception;
use Google\Service\Calendar\Event;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Directory;
use Google_Service_PeopleService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService implements GoogleCalendarServiceInterface
{
    /** @var array<string, Google_Client> */
    private array $clientCache = [];

    /**
     * 指定期間の予定を取得.
     *
     * @return Collection<int, Event>
     *
     * @throws Exception
     */
    public function listSchedule(
        string $calendarId,
        CarbonImmutable $startDate,
        CarbonImmutable $endDate
    ): Collection {
        // サービス取得.
        $service = new Google_Service_Calendar(
            $this->getClient($calendarId)
        );

        // 結果を取得.
        try {
            $results = $service
                ->events
                ->listEvents($calendarId, [
                    'timeMin'      => $startDate->toIso8601String(),
                    'timeMax'      => $endDate->toIso8601String(),
                    'singleEvents' => true,
                    'orderBy'      => 'startTime',
                ]);

            /** @var Collection<int, Event> $items */
            $items   = $results->getItems();

            return collect($items);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return collect();
        }
    }

    /**
     * 予定を作成.
     *
     * @param  Collection<int, string>  $calendarIdList
     *
     * @throws Exception
     * @throws \Google\Service\Exception
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
    ): void {
        // サービス取得.
        $service            = new Google_Service_Calendar(
            $this->getClient($authEmail)
        );

        // 全参加者のリスト.
        $attendList         = collect([$authEmail])
            ->concat($calendarIdList->toArray());

        // 終日イベントは date フィールド（翌日が終了日）、通常イベントは dateTime フィールドを使用.
        $startField         = $allDay
            ? ['date' => $start->format('Y-m-d')]
            : ['dateTime' => $start->toIso8601String(), 'timeZone' => 'Asia/Tokyo'];
        $endField           = $allDay
            ? ['date' => $end->copy()->addDay()->format('Y-m-d')]
            : ['dateTime' => $end->toIso8601String(), 'timeZone' => 'Asia/Tokyo'];

        // 登録パラメータ.
        $eventParams        = [
            'summary'         => $summary,
            'start'           => $startField,
            'end'             => $endField,
            'location'        => $location,
            'description'     => $this->buildDescription($descriptionPrefix, $description),
            'guestsCanModify' => true,
        ];

        // outOfOffice イベントは attendees フィールドを含めると malformedOutOfOfficeEvent エラーになるため除外する
        if ($eventType !== 'outOfOffice') {
            $eventParams['attendees'] = $attendList->map(function ($calendarId) use ($authEmail) {
                return $calendarId === $authEmail
                    ? ['email' => $calendarId, 'responseStatus' => 'accepted']
                    : ['email' => $calendarId];
            })->toArray();
        }

        // extendedProperties.shared に source と独自分類値を設定
        $sharedProps        = [];
        if ($source !== null) {
            $sharedProps['source'] = $source;
        }

        // Google Calendar API の公式 eventType 値（outOfOffice 等）はトップレベルに設定する。
        // 独自の分類値（other / rest / bizTrip 等）は公式フィールドではないため extendedProperties.shared に保存する。
        $officialEventTypes = ['default', 'focusTime', 'outOfOffice', 'workingLocation', 'birthday'];
        $isOfficialType     = $eventType !== null && in_array($eventType, $officialEventTypes, true);

        if ($eventType !== null && ! $isOfficialType) {
            $sharedProps['eventType'] = $eventType;
        }

        if ($sharedProps !== []) {
            $eventParams['extendedProperties'] = ['shared' => $sharedProps];
        }

        // 公式 eventType のみトップレベルに設定
        if ($isOfficialType) {
            $eventParams['eventType'] = $eventType;
        }

        $params             = new Google_Service_Calendar_Event($eventParams);

        if ($transparency !== null) {
            $params->setTransparency($transparency);
        }

        Log::debug('google calendar insert params: '.json_encode($params->toSimpleObject()));

        // 登録を実施.
        // 結果を取得.
        $result             = $service->events->insert(
            $authEmail,
            $params,
            [
                'sendUpdates' => 'all',
            ]
        );
        Log::info('google calendar insert result: transparency='.$result->getTransparency().' id='.$result->getId());
    }

    /**
     * 指定期間の予定を削除（任意フィルタで対象を限定可能）。
     *
     * @param  callable(Event):bool|null  $filter
     *
     * @throws Exception
     */
    public function deleteSchedules(string $calendarId, CarbonImmutable $startDate, CarbonImmutable $endDate, ?callable $filter = null): void
    {
        $service = new Google_Service_Calendar(
            $this->getClient($calendarId)
        );

        $events  = $this->listSchedule($calendarId, $startDate, $endDate);
        if ($filter !== null) {
            $events = $events->filter(static fn (Event $event): bool => (bool) $filter($event));
        }

        foreach ($events as $event) {
            try {
                $service->events->delete(
                    $calendarId,
                    $event->getId(),
                    ['sendUpdates' => 'all']
                );
            } catch (\Exception $e) {
                Log::warning('google calendar delete failed: '.$e->getMessage());
            }
        }
    }

    private function buildDescription(?string $prefix, string $description): string
    {
        if ($prefix === null || $prefix === '') {
            return $description;
        }

        return trim($prefix.' '.$description);
    }

    /**
     * クライアント取得.
     *
     * @throws Exception
     */
    private function getClient(string $executorCalendarId): Google_Client
    {
        if (isset($this->clientCache[$executorCalendarId])) {
            return $this->clientCache[$executorCalendarId];
        }

        // 認証パラメータ.
        $rawData            = (string) config('services.google.calendar_sa');
        $jsonKey            = (array) json_decode($rawData, true);

        // 利用スコープ.
        $scope              = [
            Google_Service_Calendar::CALENDAR,
            Google_Service_Directory::ADMIN_DIRECTORY_USER_READONLY,
            Google_Service_Directory::ADMIN_DIRECTORY_RESOURCE_CALENDAR_READONLY,
            Google_Service_PeopleService::USERINFO_PROFILE,
        ];

        // クライアント作成.
        $client             = new Google_Client;
        $client->setApplicationName('wportal カレンダー操作');
        $client->setScopes($scope);
        $client->setAuthConfig($jsonKey);
        $client->setConfig('subject', $executorCalendarId);

        $this->clientCache[$executorCalendarId] = $client;

        return $client;
    }
}

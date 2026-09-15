<?php

declare(strict_types=1);

namespace App\Console\Commands\Test;

use Carbon\Carbon;
use Google\Exception;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Directory;
use Google_Service_PeopleService;
use Illuminate\Console\Command;

class Calendar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature   = 'test:Calendar {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'カレンダーの接続チェックを行う.';

    /**
     * New a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws Exception
     */
    public function handle(): int
    {
        $this->info('カレンダーテスト、指定のアドレスの本日の予定５件を取得.');

        $executorCalendarId = $this->argument('email');
        if (! $executorCalendarId) {
            $this->info('カレンダーID（実行者のEmailアドレス）を指定してください');

            return self::FAILURE;
        }

        // 認証パラメータ.
        $rawData            = (string) config('services.google.calendar_sa');
        $jsonKey            = (string) json_decode($rawData, true);

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

        try {
            // 当日～翌日の予定を対象とする
            $startDatetime = Carbon::now()->hour(0)->minute(0);
            $endDatetime   = Carbon::now()->addDay()->hour(0)->minute(0);

            // 結果を取得.
            $service       = new Google_Service_Calendar($client);
            $calenderId    = $executorCalendarId;
            $results       = $service
                ->events
                ->listEvents($calenderId, [
                    'orderBy'      => 'startTime',
                    'singleEvents' => true,
                    'timeZone'     => config('timezone', 'Asia/Tokyo'),
                    'timeMin'      => $this->replaceToISO8601($startDatetime->toDateTimeString()),
                    'timeMax'      => $this->replaceToISO8601($endDatetime->toDateTimeString()),
                    'maxResults'   => 10,
                ]);

            // イベント一覧を取得.
            $eventList     = $results->getItems();
            foreach ($eventList as $event) {
                $start = $event->getStart()->dateTime;
                $end   = $event->getEnd()->dateTime;

                $this->info("[$event->id] ==========================");
                $this->info("kind: $event->kind");
                $this->info("eventType: $event->eventType");
                $this->info("summary: $event->summary");
                $this->info("location: $event->location");
                $this->info("start: $start");
                $this->info("end: $end");
                $this->info("etag: $event->etag");
                $this->info('');
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Y-m-d H:i:s形式からISO8601形式(ex. 2018-07-13T18:00:00+09:00)の文字列に変換する
     */
    private function replaceToISO8601(string $datetime): string
    {
        return str_replace(' ', 'T', $datetime).'+09:00';
    }
}

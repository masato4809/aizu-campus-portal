<?php

declare(strict_types=1);

namespace App\GraphQL\Usecases\Queries;

use App\Facades\External\GoogleCalendarService;
use App\Facades\Internal\PipeService;
use App\Pipe\Usecases\PipeUsecasesCalendarUserScheduleListQuery;
use Exception;
use Google\Service\Calendar\Event;

final class UsecasesCalendarUserScheduleListQuery
{
    /**
     * @param  array<mixed>  $args
     * @return array<mixed>
     *
     * @throws Exception
     */
    public function __invoke(mixed $_, array $args): array
    {
        // 早く完了しないように強制遅延.
        localSleep();

        // パイプ作成.
        /** @var PipeUsecasesCalendarUserScheduleListQuery $pipe */
        $pipe      = PipeService::insertNewPipe(
            new PipeUsecasesCalendarUserScheduleListQuery($args)
        );

        $ret       = [];

        // 渡されたメールアドレスそれぞれの予定を取得.
        foreach ($pipe->emailList as $email) {
            $eventList = GoogleCalendarService::listSchedule(
                $email,
                $pipe->startDate,
                $pipe->endDate,
            );
            $eventList
                ->reject(function (Event $event) {
                    return $event->transparency === 'transparent';
                })
                ->each(function (Event $event) use (&$ret, $email) {
                    $ret[] = [
                        'identify'     => "$email-{$event->id}",
                        'eventId'      => $event->id ?: '',
                        'calendarId'   => $email,
                        'kind'         => $event->kind ?: '',
                        'summary'      => $event->summary ?: '',
                        'start'        => $event->getStart()->dateTime ?: '',
                        'end'          => $event->getEnd()->dateTime ?: '',
                        'transparency' => $event->transparency ?: '',
                    ];
                });
        }

        return $ret;
    }
}

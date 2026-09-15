<?php

declare(strict_types=1);

namespace App\GraphQL\App\Queries;

use App\Facades\Models\App\Trn\TrnUserService;
use App\Models\App\Trn\TrnUser;

final class AppTrnUserListQuery
{
    /**
     * @param  array<mixed>  $args
     * @return array<mixed>
     */
    public function __invoke(mixed $_, array $args): array
    {
        // 早く完了しないように強制遅延.
        localSleep();

        $trnUserList = TrnUserService::list();

        return $trnUserList->map(function (TrnUser $trnUser) {
            return $trnUser->toPayload();
        })->toArray();
    }
}

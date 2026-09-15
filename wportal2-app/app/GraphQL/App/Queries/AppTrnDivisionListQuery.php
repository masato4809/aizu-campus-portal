<?php

declare(strict_types=1);

namespace App\GraphQL\App\Queries;

use App\Facades\Models\App\Trn\TrnDivisionService;
use App\Models\App\Trn\TrnDivision;

final class AppTrnDivisionListQuery
{
    /**
     * @param  array<mixed>  $args
     * @return array<mixed>
     */
    public function __invoke(mixed $_, array $args): array
    {
        // 早く完了しないように強制遅延.
        localSleep();

        $trnDivisionList = TrnDivisionService::list();

        return $trnDivisionList->map(function (TrnDivision $trnDivision) {
            return $trnDivision->toPayload();
        })->toArray();
    }
}

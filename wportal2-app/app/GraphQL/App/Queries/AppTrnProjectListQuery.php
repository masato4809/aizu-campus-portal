<?php

declare(strict_types=1);

namespace App\GraphQL\App\Queries;

use App\Facades\Models\App\Trn\TrnProjectService;
use App\Models\App\Trn\TrnProject;

final class AppTrnProjectListQuery
{
    /**
     * @param  array<mixed>  $args
     * @return array<mixed>
     */
    public function __invoke(mixed $_, array $args): array
    {
        // 早く完了しないように強制遅延.
        localSleep();

        $trnProjectList = TrnProjectService::list();

        return $trnProjectList->map(function (TrnProject $trnProject) {
            return $trnProject->toPayload();
        })->toArray();
    }
}

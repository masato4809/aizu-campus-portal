<?php

declare(strict_types=1);

namespace App\GraphQL\App\Qmart\Queries;

use App\Facades\Models\App\Trn\TrnQmartItemService;

final class AppQmartItemListQuery
{
    /**
     * @param  array<mixed>  $args
     * @return array<mixed>
     */
    public function __invoke(mixed $_, array $args): array
    {
        // 早く完了しないように強制遅延.
        localSleep();

        return TrnQmartItemService::offsetPagination(
            (int) $args['index'],
            (int) $args['step'],
            [
                'TrnUser',
                'TrnQmartItemImage',
            ]
        );
    }
}

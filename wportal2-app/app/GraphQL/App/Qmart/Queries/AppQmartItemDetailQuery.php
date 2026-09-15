<?php

declare(strict_types=1);

namespace App\GraphQL\App\Qmart\Queries;

use App\Models\App\Trn\TrnQmartItem;

final class AppQmartItemDetailQuery
{
    /**
     * @param  array<mixed>  $args
     * @return array<mixed>|null
     */
    public function __invoke(mixed $_, array $args): ?array
    {
        // 早く完了しないように強制遅延.
        localSleep();

        $with         = [
            'TrnUser',
            'TrnQmartItemImage',
            'TrnQmartComment',
            'TrnQmartComment.TrnUser',
        ];

        /** @var TrnQmartItem|null $trnQmartItem */
        $trnQmartItem = TrnQmartItem::query()
            ->with($with)
            ->find((int) $args['id']);

        return $trnQmartItem?->toPayload($with);
    }
}

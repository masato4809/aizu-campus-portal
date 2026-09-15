<?php

declare(strict_types=1);

namespace App\GraphQL\Sample\Queries;

use App\Facades\Internal\PipeService;
use App\Facades\Models\App\Trn\TrnUserService;
use App\Pipe\Sample\PipeSamplePaginationQuery;

final class SamplePaginationQuery
{
    /**
     * @param  array<mixed>  $args
     * @return array<mixed>
     */
    public function __invoke(mixed $_, array $args): array
    {
        // 早く完了しないように強制遅延.
        localSleep(1);

        // パイプ作成.
        /** @var PipeSamplePaginationQuery $pipe */
        $pipe = PipeService::insertNewPipe(
            new PipeSamplePaginationQuery($args)
        );

        return TrnUserService::offsetPagination(
            $pipe->pageIndex,
            $pipe->pageStep,
        );
    }
}

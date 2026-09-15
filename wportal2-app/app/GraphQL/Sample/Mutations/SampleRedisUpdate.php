<?php

declare(strict_types=1);

namespace App\GraphQL\Sample\Mutations;

use App\Facades\Internal\PipeService;
use App\GraphQL\Sample\Queries\SampleRedisQuery;
use App\Pipe\Sample\PipeSampleRedisUpdate;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

final class SampleRedisUpdate
{
    /**
     * @param  array<mixed>  $args
     * @return array<mixed>
     *
     * @throws InvalidArgumentException
     */
    public function __invoke(mixed $_, array $args): array
    {
        // 早く完了しないように強制遅延.
        localSleep(1);

        // パイプ作成.
        /** @var PipeSampleRedisUpdate $pipe */
        $pipe = PipeService::insertNewPipe(
            new PipeSampleRedisUpdate($args)
        );

        // バリデーションチェック.
        if (! PipeService::isValid()) {
            return PipeService::getValidationResult();
        }

        // キャッシュに値を保存.
        Cache::set(SampleRedisQuery::REDIS_QUERY_STORE_KEY, $pipe->storeValue, 3600);

        return PipeService::getResult();
    }
}

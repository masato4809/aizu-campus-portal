<?php

declare(strict_types=1);

namespace App\GraphQL\Sample\Queries;

use App\Facades\Internal\PipeService;
use App\Pipe\Sample\PipeSampleRedisQuery;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

final class SampleRedisQuery
{
    public const string REDIS_QUERY_STORE_KEY = 'redis_query_store_key';

    /**
     * @param  array<mixed>  $args
     * @return array<mixed>
     */
    public function __invoke(mixed $_, array $args): array
    {
        // 早く完了しないように強制遅延.
        localSleep(1);

        // パイプ作成.
        /** @var PipeSampleRedisQuery $pipe */
        $pipe       = PipeService::insertNewPipe(
            new PipeSampleRedisQuery($args)
        );

        // サンプル表示.
        Log::info('引数 => '.$pipe->sampleArgument);

        // キャッシュ（redis）から値取得.
        $storeValue = Cache::get(self::REDIS_QUERY_STORE_KEY, function () {
            return '[未設定]';
        });

        return [
            'storeValue' => $storeValue,
        ];
    }
}

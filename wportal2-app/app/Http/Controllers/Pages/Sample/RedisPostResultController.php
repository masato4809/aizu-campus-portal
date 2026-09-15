<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages\Sample;

use App\GraphQL\Sample\Queries\SampleRedisQuery;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Response;
use Psr\SimpleCache\InvalidArgumentException;

class RedisPostResultController extends Controller
{
    /**
     * @throws InvalidArgumentException
     */
    public function invoke(Request $request): Response
    {
        // POSTの値を取得.
        $storeValue = $request->input('store_value', '');

        // キャッシュに値を保存.
        Cache::set(SampleRedisQuery::REDIS_QUERY_STORE_KEY, $storeValue, 3600);

        return $this->render('Sample/RedisPostResult/Index', [
            'storeValue' => $storeValue,
        ]);
    }
}

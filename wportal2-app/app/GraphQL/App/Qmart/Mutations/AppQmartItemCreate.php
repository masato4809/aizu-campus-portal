<?php

declare(strict_types=1);

namespace App\GraphQL\App\Qmart\Mutations;

use App\Enum\App\EStatusCode;

/**
 * Qマート: 出品登録.
 *
 * @note タスク6で実装予定。現在は仮のResolverクラス.
 */
final class AppQmartItemCreate
{
    /**
     * @param  array<mixed>  $args
     * @return array<mixed>
     */
    public function __invoke(mixed $_, array $args): array
    {
        // 早く完了しないように強制遅延.
        localSleep();

        // TODO: タスク6で実装.
        return [
            'statusCode'    => EStatusCode::BAD_REQUEST->value,
            'statusMessage' => '未実装です',
            'errors'        => null,
        ];
    }
}

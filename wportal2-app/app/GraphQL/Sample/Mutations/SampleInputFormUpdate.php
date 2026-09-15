<?php

declare(strict_types=1);

namespace App\GraphQL\Sample\Mutations;

use App\Facades\Internal\PipeService;
use App\Pipe\Sample\PipeSampleInputFormUpdate;

final class SampleInputFormUpdate
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
        PipeService::insertNewPipe(
            new PipeSampleInputFormUpdate($args)
        );

        // バリデーションチェック.
        if (! PipeService::isValid()) {
            return PipeService::getValidationResult();
        }

        // NOTE: 更新処理はここで実施する想定.

        return PipeService::getResult();
    }
}

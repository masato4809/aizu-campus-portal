<?php

declare(strict_types=1);

namespace App\GraphQL\App\Mutations;

use App\Facades\External\SlackService;
use App\Facades\Internal\PipeService;
use App\Facades\Models\App\Trn\TrnUserSlackProfileService;
use App\Pipe\App\PipeAppPersonalSettingShowAlignSlack;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final class AppPersonalSettingShowAlignSlack
{
    /**
     * @param  array<mixed>  $args
     * @return array<mixed>
     *
     * @throws Throwable
     */
    public function __invoke(mixed $_, array $args): array
    {
        // 早く完了しないように強制遅延.
        localSleep();

        // パイプ作成.
        PipeService::insertNewPipe(
            new PipeAppPersonalSettingShowAlignSlack($args)
        );

        // バリデーションチェック.
        if (! PipeService::isValid()) {
            return PipeService::getValidationResult();
        }

        // 更新処理.
        try {
            DB::transaction(static function (): void {
                // SlackAPIでユーザー情報を取得.
                SlackService::updateByPipe();

                // Slack情報の更新.
                TrnUserSlackProfileService::updateByPipe();
            });
        } catch (Exception $e) {
            // throw内容を出力する.
            Log::error($e->getMessage());
        }

        return PipeService::getResult();
    }
}

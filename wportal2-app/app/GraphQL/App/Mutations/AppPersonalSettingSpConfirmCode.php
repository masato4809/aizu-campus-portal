<?php

declare(strict_types=1);

namespace App\GraphQL\App\Mutations;

use App\Enum\App\EStatusCode;
use App\Facades\Internal\PipeService;
use App\Facades\Models\App\Auth\AuthUserService;
use App\Pipe\App\PipeAppPersonalSettingSpConfirmCode;
use Illuminate\Support\Facades\DB;
use Throwable;

final class AppPersonalSettingSpConfirmCode
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
        /** @var PipeAppPersonalSettingSpConfirmCode $pipe */
        $pipe = PipeService::insertNewPipe(
            new PipeAppPersonalSettingSpConfirmCode($args)
        );

        // バリデーションチェック.
        if (! PipeService::isValid()) {
            return PipeService::getValidationResult();
        }

        // 更新処理.
        DB::transaction(static function () {
            // auth_userのログイン許可を更新する.
            AuthUserService::updateByPipe();
        });

        return PipeService::getResult([
            'statusCode' => $pipe->isValid
                ? (string) EStatusCode::OK->value
                : (string) EStatusCode::UNPROCESSABLE_ENTITY->value,
        ]);
    }
}

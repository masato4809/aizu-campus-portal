<?php

declare(strict_types=1);

namespace App\GraphQL\App\Mutations;

use App\Facades\Internal\PipeService;
use App\Facades\Models\App\Auth\AuthUserService;
use App\Pipe\App\PipeAppPersonalSettingSpPassword;
use Illuminate\Support\Facades\DB;
use Throwable;

final class AppPersonalSettingSpPassword
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
        /** @var PipeAppPersonalSettingSpPassword $pipe */
        $pipe = PipeService::insertNewPipe(
            new PipeAppPersonalSettingSpPassword($args)
        );

        // バリデーションチェック.
        if (! PipeService::isValid()) {
            return PipeService::getValidationResult([
                'url' => '',
            ]);
        }

        // 更新処理.
        DB::transaction(static function () {
            // auth_userのpasswordを更新する.
            AuthUserService::updateByPipe();
        });

        return PipeService::getResult([
            'url' => $pipe->resultUrl,
        ]);
    }
}

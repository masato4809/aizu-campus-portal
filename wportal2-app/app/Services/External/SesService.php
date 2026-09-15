<?php

declare(strict_types=1);

namespace App\Services\External;

use App\Services\External\SesService\SesParameter;
use AWS;
use Aws\Ses\SesClient;
use Illuminate\Support\Facades\Log;

/**
 * SesServiceに対する処理
 */
class SesService
{
    /**
     * メールの送信を実施
     */
    public function sendMail(SesParameter $param): void
    {
        // AWSのconfig確認.
        $key    = config('aws.credentials.key');
        if (empty($key)) {
            Log::error('AWSのconfigが設定されていません');

            return;
        }

        /** @var SesClient $client */
        $client = AWS::createClient('ses');

        // パラメータのチェック
        if (! $param->isValid()) {
            return;
        }

        // メールの送信を実行
        $client->sendEmail($param->getParameterArray());
    }
}

<?php

declare(strict_types=1);

namespace App\Logging;

use Illuminate\Log\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerWrapper;

class LogDebugFormatter
{
    public function __invoke(Logger $logging): void
    {
        /* フォーマットを指定 */
        $format        = '%datetime% [%channel%.%level_name%] %extra.class%@%extra.function%(%extra.line%) - %message%'.PHP_EOL;
        /* 日付のフォーマットを指定 */
        $dateFormat    = 'Y/m/d H:i:s';
        /* フォーマットを作成 */
        $lineFormatter = new LineFormatter($format, $dateFormat, true, true);
        /* 各ハンドラにフォーマットを代入 */
        foreach ($logging->getHandlers() as $handler) {
            /** @var HandlerWrapper $handler */
            $handler->setFormatter($lineFormatter);
        }
    }
}

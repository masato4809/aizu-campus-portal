<?php

declare(strict_types=1);

namespace App\Logging;

use Illuminate\Log\Logger;
use Monolog\Handler\HandlerWrapper;
use Monolog\Processor\IntrospectionProcessor;

class LogDebugProcessor
{
    public function __invoke(Logger $logging): void
    {
        /* プロセッサーを作成 */
        $introspectionProcessor = new IntrospectionProcessor(
            \Monolog\Logger::DEBUG,
            [],
            4
        );
        /* ログの各ハンドラにプロセッサーを設定する */
        foreach ($logging->getHandlers() as $handler) {
            /** @var HandlerWrapper $handler */
            $handler->pushProcessor($introspectionProcessor);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Logging;

use Illuminate\Log\Logger;
use Monolog\Handler\HandlerWrapper;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Processor\WebProcessor;

class CustomizeFormatter
{
    /**
     * 渡されたloggerをカスタマイズする
     */
    public function __invoke(Logger $logger): void
    {
        $formatter = new JsonCustomizeFormatter;
        $formatter->includeStacktraces();

        // extraフィールドを追加
        $backtrace = new IntrospectionProcessor(\Monolog\Logger::DEBUG, ['Illuminate\\']);
        // extraフィールドにurl, ip, http_method, server, referrer追加
        $wp        = new WebProcessor;
        // extraフィールドにprocess_id追加
        $pid       = new ProcessIdProcessor;

        foreach ($logger->getHandlers() as $handler) {
            /** @var HandlerWrapper $handler */
            $handler->setFormatter($formatter);
            $handler->pushProcessor($backtrace);
            $handler->pushProcessor($wp);
            $handler->pushProcessor($pid);
            // extraにオリジナル項目追加
            // $handler->pushProcessor([$this, 'addAuthID']);
        }
    }

    /**
     * 認証IDをログ出力に付加
     */
    //    public function addAuthID(array $record): array
    //    {
    //        /** @var AuthUser $auth */
    //        $auth = Auth::user();
    //
    //        $authId = $auth ? $auth->id : '';
    //        $record['extra']['auth_id'] = $authId;
    //
    //        return $record;
    //    }
}

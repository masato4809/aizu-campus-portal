<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages\Sample;

use App\Facades\External\SlackService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Inertia\Response;

class SlackLogController extends Controller
{
    public function invoke(): Response
    {
        // 表示用ファイルパス.
        $filePath = str_replace(app_path(), '', __FILE__);

        // Noticeチャンネルへの通知.
        SlackService::notifyNotice('Laravel-Inertia-TemplateのNoticeテストメッセージ', '指定タイトル', $filePath);

        // Errorチャンネルへの通知.
        SlackService::notifyError('Laravel-Inertia-TemplateのErrorテストメッセージ', '指定タイトル', $filePath);

        // 標準ログの出力.
        Log::info('Laravel-Inertia-TemplateのLaravel標準ログ');

        // 標準エラーログの出力.
        Log::error('Laravel-Inertia-TemplateのLaravelエラーログ');

        return $this->render('Sample/SlackLog/Index');
    }
}

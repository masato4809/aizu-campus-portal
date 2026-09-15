<?php

declare(strict_types=1);

namespace App\Console\Commands\Test;

use App\Facades\External\SlackService;
use Illuminate\Console\Command;

class SendSlack extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature   = 'test:SendSlack';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Slackへの送信テストを行う';

    /**
     * New a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // 表示用ファイルパス.
        $filePath = str_replace(app_path(), '', __FILE__);

        // Noticeチャンネルへの通知.
        SlackService::notifyNotice('wportal2のNoticeテストメッセージ', '指定タイトル', $filePath);

        // Errorチャンネルへの通知.
        SlackService::notifyError('wportal2のErrorテストメッセージ', '指定タイトル', $filePath);

        return self::SUCCESS;
    }
}

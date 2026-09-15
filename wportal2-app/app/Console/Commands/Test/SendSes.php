<?php

declare(strict_types=1);

namespace App\Console\Commands\Test;

use App\Facades\External\SesService;
use App\Services\External\SesService\SesParameter;
use Illuminate\Console\Command;

class SendSes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature   = 'test:SendSes {to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sesへの送信テストを行う';

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
        // 引数より対象のemailを取得.
        $toAddress = $this->argument('to');
        if ($toAddress == null) {
            return self::FAILURE;
        }
        $this->info("送信対象:$toAddress");

        // 送信パラメータの作成.
        $param     = new SesParameter;
        $param->setFrom('no-reply@wportal.org');
        $param->setTo($toAddress);
        $param->setSubject('テストメール');
        $param->setMessageBody('これはテストメールです。');

        // 送信の実施.
        SesService::sendMail($param);

        return self::SUCCESS;
    }
}

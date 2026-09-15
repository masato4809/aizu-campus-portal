<?php

declare(strict_types=1);

namespace App\Console\Commands\Batch;

use App\Enum\App\EBatchRelease;
use App\Facades\Models\App\Trn\TrnBatchReleaseHistoryService;
use App\Models\App\Trn\TrnBatchReleaseHistory;
use Database\Seeders\Batch\Mst\BatchSeeder;
use Illuminate\Console\Command;

class BatchRelease extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature   = 'batch:BatchRelease {identify} {--dryRun=1} {--confirmLimit=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'リリース時に実施するデータ更新などのバッチ';

    /**
     * Create a new command instance.
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
        // 識別子を取得.
        $identify       = $this->argument('identify');
        if ($identify == null) {
            return self::FAILURE;
        }

        // 識別子を判定.
        $eIdentify      = EBatchRelease::tryFrom($identify);
        if ($eIdentify == null) {
            $this->error("識別子の指定が不正です:$identify");

            return self::FAILURE;
        }

        // dryRunフラグ.
        $dryRun         = (bool) ($this->option('dryRun'));

        // 上限確認フラグ.
        $confirmLimit   = (bool) ($this->option('confirmLimit'));

        // 開始表示.
        $this->info("バッチ[$identify]を実行します");
        $this->info('dryRun：'.($dryRun ? 'Yes' : 'No'));
        $this->info('confirmLimit:'.($confirmLimit ? 'Yes' : 'No'));
        if ($dryRun) {
            $this->info('dryRunモードのため、更新内容は記録されません');
        }

        // 対象スクリプトの取得.
        $class          = $eIdentify->class();

        // インスタンス作成.
        /** @var BatchSeeder $instance */
        $instance       = new $class;

        // 対象スクリプトの実行上限を取得.
        $executionLimit = $eIdentify->executionLimit();

        // 対象のスクリプトの実行回数を取得.
        $history        = TrnBatchReleaseHistoryService::findByBatchIdentify($eIdentify);
        $executionNum   = $history ? $history->execution_count : 0;

        // 上限などのチェック.
        if (! $dryRun && $eIdentify->hasLimit()) {
            if ($executionNum >= $executionLimit) {
                $this->error("識別子の実行回数が上限に達しています:$identify");
                $this->error("実行回数:$executionNum");
                $this->error("上限回数:$executionLimit");

                return self::FAILURE;
            }
        }

        // 処理の実行.
        $instance->setCommand($this);
        $instance->run($dryRun);

        // 実施履歴の記録.
        if (! $dryRun) {
            if ($history === null) {
                // 新規登録.
                $history                  = new TrnBatchReleaseHistory;
                $history->batch_identify  = $eIdentify;
                $history->execution_count = 1;
                TrnBatchReleaseHistoryService::insertOrFail($history);
            } else {
                // 更新.
                $history->execution_count += 1;
                TrnBatchReleaseHistoryService::updateOrFail($history);
            }

            $this->info("バッチ[$identify]の実行をDBに記録しました");
        }

        $this->info("バッチ[$identify]を実行しました");

        return self::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Commands\Batch;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature   = 'batch:FixMigrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrationの不具合を修正する';

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
        $this->info('Migrationの不具合を修正します');

        // wportal1で作成済みのため
        //        $dataArray = [
        //            ['migration' => '2024_04_01_000001_create_auth_user_table', 'batch' => 1],
        //            ['migration' => '2024_04_01_000002_create_trn_user_table', 'batch' => 1],
        //            ['migration' => '2024_04_01_000003_create_trn_user_slack_profile_table', 'batch' => 1],
        //            ['migration' => '2024_04_01_000004_create_trn_division_table', 'batch' => 1],
        //            ['migration' => '2024_04_01_000005_create_trn_division_user_table', 'batch' => 1],
        //            ['migration' => '2024_04_01_000006_create_trn_project_table', 'batch' => 1],
        //            ['migration' => '2024_04_01_000007_create_trn_project_user_table', 'batch' => 1],
        //            ['migration' => '2024_04_01_000008_create_trn_attendance_state_table', 'batch' => 1],
        //            ['migration' => '2024_04_01_000009_create_trn_user_project_priority_table', 'batch' => 1],
        //            ['migration' => '2024_04_01_000010_create_trn_user_division_priority_table', 'batch' => 1],
        //            ['migration' => '2024_04_01_000011_create_trn_user_authority_table', 'batch' => 1],
        //            ['migration' => '2024_04_01_000012_create_trn_project_notification_table', 'batch' => 1],
        //        ];

        // wportal1で作成済みのため.
        $dataArray = [
            ['migration' => '2024_04_01_000013_add_column_auth_user_table', 'batch' => 1],
        ];

        DB::table('migrations')
            ->insert($dataArray);

        $this->info('Migrationの不具合を修正しました');

        return self::SUCCESS;
    }
}

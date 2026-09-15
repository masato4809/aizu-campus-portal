<?php

declare(strict_types=1);

namespace Database\Seeders\Batch\Mst\Init;

use App\Models\App\Mst\MstReward;
use Database\Seeders\Batch\Mst\BatchSeeder;
use Database\Seeders\Batch\Mst\Init\App\Seeder_MstReward;
use Throwable;

class MstInitReward extends BatchSeeder
{
    /**
     * シードするマスタを定義.
     */
    const MODELS = [
        MstReward::class,
    ];

    /**
     * Run the database seeds.
     *
     * @throws Throwable
     */
    public function run(bool $dryRun): void
    {
        if ($dryRun) {
            $this->command->info('dryRunモードのため、何もせずに処理終了します');

            return;
        }

        ini_set('memory_limit', '2G');

        $this->command->info('begin ['.__CLASS__.']');

        // 実行するコレクションを準備.
        $modelList = collect(self::MODELS);

        // 初期化前にデータ削除を行う.
        $modelList->each(function (string $model) {
            $function = "$model::truncate";
            if (is_callable($function)) {
                call_user_func($function);
            }
        });

        // データ初期化を実施.
        $this->call([
            Seeder_MstReward::class,
        ]);

        $this->command->info('done ['.__CLASS__.']');
    }
}

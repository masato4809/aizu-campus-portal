<?php

declare(strict_types=1);

namespace Database\Seeders\Batch\Mst;

use Illuminate\Database\Seeder;

/**
 * バッチ用Seeder基底クラス.
 */
abstract class BatchSeeder extends Seeder
{
    /**
     * Seeder実行処理.
     */
    abstract public function run(bool $dryRun): void;
}

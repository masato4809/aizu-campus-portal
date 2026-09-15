<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\App\Trn\TrnSeat;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Random\RandomException;
use Symfony\Component\Console\Helper\ProgressBar;
use Throwable;

class SeatSeeder extends Seeder
{
    private ?ProgressBar $pb    = null;

    private string $pbModelName = '';

    /**
     *  ローカル/テストケース向けの初期データを用意する.
     *
     * @throws RandomException
     * @throws Throwable
     */
    public function run(): void
    {
        $this->command->info('******* initialize local/test data. ********');

        // local/tst/dev環境でなければ初期化実行不可.
        if (! (isLocal() || isTst() || isDev())) {
            $this->command->info('!!! caution local/tst/dev environment only. !!!');

            return;
        }

        TrnSeat::truncate();

        $instance     = $this;

        $this->beginFactory('TrnSeat', 194);

        // 9101を(0,0)として処理する.
        $blockSize    = 100;

        // 9006 - 9002.
        TrnSeat::factory(5)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9006 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => -1500,
                    'position_y'   => $sequence->index * $blockSize + 300,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9012 - 9008.
        TrnSeat::factory(5)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9012 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => -1400,
                    'position_y'   => $sequence->index * $blockSize + 300,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9021 - 9014.
        TrnSeat::factory(8)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9021 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => -1250,
                    'position_y'   => $sequence->index * $blockSize,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9029 - 9022.
        TrnSeat::factory(8)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9029 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => -1150,
                    'position_y'   => $sequence->index * $blockSize,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9037 - 9030.
        TrnSeat::factory(8)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9037 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => -1000,
                    'position_y'   => $sequence->index * $blockSize,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9045 - 9038.
        TrnSeat::factory(8)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9045 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => -900,
                    'position_y'   => $sequence->index * $blockSize,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9053 - 9046.
        TrnSeat::factory(8)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9053 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => -750,
                    'position_y'   => $sequence->index * $blockSize,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9061 - 9054.
        TrnSeat::factory(8)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9061 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => -650,
                    'position_y'   => $sequence->index * $blockSize,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9069 - 9062.
        TrnSeat::factory(8)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9069 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => -500,
                    'position_y'   => $sequence->index * $blockSize,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9077 - 9070.
        TrnSeat::factory(8)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9077 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => -400,
                    'position_y'   => $sequence->index * $blockSize,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9085 - 9078.
        TrnSeat::factory(8)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9085 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => -250,
                    'position_y'   => $sequence->index * $blockSize,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9093 - 9086.
        TrnSeat::factory(8)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9093 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => -150,
                    'position_y'   => $sequence->index * $blockSize,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9101 - 9094.
        TrnSeat::factory(8)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9101 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => 0,
                    'position_y'   => $sequence->index * $blockSize,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9109 - 9102.
        TrnSeat::factory(8)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9109 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => 100,
                    'position_y'   => $sequence->index * $blockSize,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9117 - 9110.
        TrnSeat::factory(8)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9117 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => 250,
                    'position_y'   => $sequence->index * $blockSize,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9125 - 9118.
        TrnSeat::factory(8)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9125 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => 350,
                    'position_y'   => $sequence->index * $blockSize,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9135 - 9126.
        TrnSeat::factory(10)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9135 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => 500,
                    'position_y'   => $sequence->index * $blockSize - 200,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9177 - 9174.
        TrnSeat::factory(4)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9177 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => 500,
                    'position_y'   => $sequence->index * $blockSize - 700,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9145 - 9136.
        TrnSeat::factory(10)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9145 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => 600,
                    'position_y'   => $sequence->index * $blockSize - 200,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9181 - 9178.
        TrnSeat::factory(4)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9181 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => 600,
                    'position_y'   => $sequence->index * $blockSize - 700,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9152 - 9146.
        TrnSeat::factory(7)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9152 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => 750,
                    'position_y'   => $sequence->index * $blockSize - 200,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9185 - 9182.
        TrnSeat::factory(4)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9185 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => 750,
                    'position_y'   => $sequence->index * $blockSize - 700,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9159 - 9153.
        TrnSeat::factory(7)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9159 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => 850,
                    'position_y'   => $sequence->index * $blockSize - 200,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9189 - 9186.
        TrnSeat::factory(4)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9189 - -$sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => 850,
                    'position_y'   => $sequence->index * $blockSize - 700,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9166 - 9160.
        TrnSeat::factory(7)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9166 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => 1000,
                    'position_y'   => $sequence->index * $blockSize - 200,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9193 - 9190.
        TrnSeat::factory(4)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9193 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => 1000,
                    'position_y'   => $sequence->index * $blockSize - 700,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9173 - 9167.
        TrnSeat::factory(7)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9173 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => 1100,
                    'position_y'   => $sequence->index * $blockSize - 200,
                    'phone_number' => $phone,
                ];
            })->create();

        // 9197 - 9194.
        TrnSeat::factory(4)
            ->sequence(function (Sequence $sequence) use ($instance, $blockSize) {
                $phone = 9197 - $sequence->index;
                $instance->advanceFactory();

                return [
                    'label'        => $phone,
                    'position_x'   => 1100,
                    'position_y'   => $sequence->index * $blockSize - 700,
                    'phone_number' => $phone,
                ];
            })->create();

        $this->endFactory();

        $this->command->info('******* initialize done. ********');
    }

    /**
     * 生成ブロックの開始.
     */
    private function beginFactory(string $modelName, int $count): void
    {
        $this->pbModelName = $modelName;
        $this->command->info("begin: $this->pbModelName");
        $this->pb          = $this->command->getOutput()->createProgressBar($count);
    }

    /**
     * 生成ブロックの進行.
     */
    private function advanceFactory(): void
    {
        $this->pb?->advance();
    }

    /**
     * 生成ブロックの終了.
     */
    private function endFactory(): void
    {
        $this->pb?->finish();
        $this->command->info(" done: $this->pbModelName");
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Commands\Batch;

use App\Enum\App\ShuffleLunch\ECalculate;
use App\Facades\Usecases\UsecasesShuffleLunchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

class ShuffleLunch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature   = 'batch:ShuffleLunch {mode} {value?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'シャッフルランチに関する処理';

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
     *
     * @throws InvalidArgumentException
     */
    public function handle(): int
    {
        $mode = (string) $this->argument('mode');

        return match ($mode) {
            'check' => $this->modeCheck(),
            'set'   => $this->modeSet(),
            default => self::SUCCESS,
        };
    }

    /**
     * 現在の値をチェックする.
     */
    private function modeCheck(): int
    {
        $this->info('シャッフルランチの現在の状態を取得');
        $currentValue = Cache::get(UsecasesShuffleLunchService::getRedisKey(), ECalculate::INVALID->value);

        $this->info("現在の状態:$currentValue");

        return self::SUCCESS;
    }

    /**
     * 値を設定.
     *
     * @throws InvalidArgumentException
     */
    private function modeSet(): int
    {
        $value = (int) ($this->argument('value') ?? 0);
        if ($value === ECalculate::INVALID->value) {
            return self::FAILURE;
        }

        $this->info("シャッフルランチの値を設定:$value");
        Cache::set(
            UsecasesShuffleLunchService::getRedisKey(),
            $value,
            UsecasesShuffleLunchService::getRedisTTL());

        return self::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Commands\Batch;

use App\Enum\Mst\EMstReward;
use App\Facades\Models\App\Trn\TrnUserRewardService;
use App\Models\App\EloquentBuilder;
use App\Models\App\Trn\TrnUser;
use App\Pipe\Command\Batch\PipeCommandBatchRewardExistUser;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class RewardExistUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature   = 'batch:RewardExistUser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '作成済みで、初期化リワードを持っていないユーザーに付与を実行する.';

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
        // 対象ユーザーの取得.
        /** @var Collection<int, TrnUser> $targetList */
        $targetList = TrnUser::query()
            ->alive()
            ->whereDoesntHave('TrnUserReward', function (EloquentBuilder $query) {
                $query->where('mst_reward_id', EMstReward::CREATE_ACCOUNT->value);
            })->get();

        // 対象ユーザーの報酬獲得.
        $targetList->each(function (TrnUser $trnUser) {
            TrnUserRewardService::achieve(
                new PipeCommandBatchRewardExistUser([]),
                $trnUser->id,
                EMstReward::CREATE_ACCOUNT
            );
        });

        return self::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Commands\Batch;

use App\Enum\Mst\EMstReward;
use App\Models\App\EloquentBuilder;
use App\Models\App\Trn\TrnUserReward;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class RewardDuplicateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature   = 'batch:RewardDuplicateUser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'リワードの二重付与を確認する.';

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
        // 対象リワードの獲得.
        /** @var Collection<int, TrnUserReward> $targetList */
        $targetList = TrnUserReward::query()
            ->orWhere(function (EloquentBuilder $query) {
                $query->where('mst_reward_id', EMstReward::CREATE_ACCOUNT->value)
                    ->where('achievement_count', '>', 1);
            })
            ->orWhere(function (EloquentBuilder $query) {
                $query->where('mst_reward_id', EMstReward::UPDATE_PROFILE->value)
                    ->where('achievement_count', '>', 1);
            })
            ->get();

        // 対象ユーザーの表示.
        $this->info('Duplicate User List ====');
        $targetList->each(function (TrnUserReward $trnUserReward) {
            $trnUserId   = $trnUserReward->trn_user_id;
            $mstRewardId = $trnUserReward->mst_reward_id;
            $this->info("id:[$trnUserId] reward:[$mstRewardId]");
        });

        return self::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\Console\Commands\Batch;

use App\Console\Commands\Batch\ShuffleLunchMatching\GroupPack;
use App\Enum\App\ShuffleLunch\ECalculate;
use App\Enum\App\ShuffleLunch\EEventTimeZone;
use App\Facades\Models\App\Trn\TrnShuffleLunchEntryService;
use App\Facades\Models\App\Trn\TrnShuffleLunchGroupService;
use App\Facades\Usecases\UsecasesShuffleLunchService;
use App\Models\App\Trn\TrnShuffleLunchEntry;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Psr\SimpleCache\InvalidArgumentException;
use Throwable;

class ShuffleLunchMatching extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature   = 'batch:ShuffleLunchMatching';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'シャッフルランチのマッチングを実施';

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
     * @throws Throwable
     */
    public function handle(): int
    {
        // マッチ結果.
        /** @var Collection<int, GroupPack> $matchingList */
        $matchingList      = new Collection;

        // マッチング処理を実施.
        $eventTimeZoneList = EEventTimeZone::cases();
        foreach ($eventTimeZoneList as $eventTimeZone) {
            if ($eventTimeZone === EEventTimeZone::INVALID) {
                continue;
            }

            $matchingList->add($this->matching($eventTimeZone));
        }

        // トランザクション処理.
        DB::transaction(function () use ($matchingList) {
            // ランチグループを削除.
            TrnShuffleLunchGroupService::clearGroup();

            // グループパックからランチグループを作成.
            $matchingList->each(function (GroupPack $groupPack) {
                TrnShuffleLunchGroupService::createByGroupPack($groupPack);
            });
        });

        // 計算済みを登録.
        Cache::set(
            UsecasesShuffleLunchService::getRedisKey(),
            ECalculate::ESTABLISHED->value,
            UsecasesShuffleLunchService::getRedisTTL()
        );

        return self::SUCCESS;
    }

    /**
     * 指定タイムゾーンのマッチング処理を実施.
     *
     * @throws Throwable
     */
    private function matching(EEventTimeZone $eventTimeZone): GroupPack
    {
        // 対象となるユーザーの取得.
        /** @var Collection<int, TrnShuffleLunchEntry> $targetUserList */
        $targetUserList = TrnShuffleLunchEntryService::listTodayByEventTimeZone(
            $eventTimeZone
        );

        // グループパックを作成.
        $groupPack      = GroupPack::createGroupPackByTotal($eventTimeZone, $targetUserList->count());

        // 作成したグループパックにメンバーを当てはめていく.
        $targetUserList
            ->shuffle()
            ->each(function (TrnShuffleLunchEntry $entry) use ($groupPack) {
                $group = $groupPack->findAvailableGroup();
                if (is_null($group)) {
                    Log::error('fatal, group is full.');

                    return true;
                }

                // エントリ情報のユーザーをグループに追加.
                $group->addUser($entry->trn_user_id);

                return true;
            });

        return $groupPack;
    }
}

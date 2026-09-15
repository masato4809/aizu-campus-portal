<?php

declare(strict_types=1);

namespace App\Console\Commands\Batch\ShuffleLunchMatching;

use App\Enum\App\ShuffleLunch\EEventTimeZone;
use App\Enum\App\ShuffleLunch\EGroupType;
use Illuminate\Support\Collection;

class GroupPack
{
    private EEventTimeZone $eventTimeZone = EEventTimeZone::INVALID;

    /** @var Collection<int, Group> */
    private Collection $groupList;

    /**
     * @param  Collection<int, Group>  $groupList
     */
    public function __construct(EEventTimeZone $eventTimeZone, Collection $groupList)
    {
        $this->eventTimeZone = $eventTimeZone;
        $this->groupList     = $groupList;
    }

    /**
     * 合計人数を指定してgroupPackを作成する.
     */
    public static function createGroupPackByTotal(
        EEventTimeZone $eventTimeZone,
        int $total
    ): GroupPack {
        // 2人以下は成立しない.
        if ($total <= 2) {
            $ret = new GroupPack($eventTimeZone, new Collection);
            $ret->AssignGroupId();

            return $ret;
        }

        // 3人の場合は3人グループが1つ.
        if ($total === 3) {
            $ret = new GroupPack($eventTimeZone, new Collection(
                [new Group(EGroupType::THREE)]
            ));
            $ret->AssignGroupId();

            return $ret;
        }

        // 4人の場合は4人グループが1つ.
        if ($total === 4) {
            $ret = new GroupPack($eventTimeZone, new Collection(
                [new Group(EGroupType::FOUR)]
            ));
            $ret->AssignGroupId();

            return $ret;
        }

        // 5人の場合は5人グループが1つ.
        if ($total === 5) {
            $ret = new GroupPack($eventTimeZone, new Collection(
                [new Group(EGroupType::FIVE)]
            ));
            $ret->AssignGroupId();

            return $ret;
        }

        // 再帰的にグループリストを取得
        $groupList = new Collection;
        self::createGroupRecursive($groupList, $total);

        // グループ番号を付与して完了.
        $ret       = new GroupPack($eventTimeZone, $groupList);
        $ret->AssignGroupId();

        return $ret;
    }

    /**
     * 渡された人数からグループリストを作成する.
     *
     * @param  Collection<int, Group>  $ret
     */
    private static function createGroupRecursive(Collection &$ret, int $total): void
    {
        // 想定はないが残りの値が2以下の場合は処理不可能.
        if ($total <= 2) {
            return;
        }

        // mod4が0なら商だけ4のグループが存在.
        if ($total % 4 === 0) {
            for ($i = 0; $i < $total / 4; $i++) {
                $ret->add(
                    new Group(EGroupType::FOUR)
                );
            }
        }

        // mod4が3の時は商だけ4が存在し、3が一つ.
        if ($total % 4 === 3) {
            for ($i = 0; $i < ($total - 3) / 4; $i++) {
                $ret->add(
                    new Group(EGroupType::FOUR)
                );
            }
            $ret->add(
                new Group(EGroupType::THREE)
            );
        }

        // mod4が1か2なら3のグループカウントを+1してから、3を引いて、再帰処理
        if ($total % 4 === 1 || $total % 4 === 2) {
            $ret->add(
                new Group(EGroupType::THREE)
            );
            self::createGroupRecursive($ret, $total - 3);
        }
    }

    /**
     * イベントのタイムゾーンを取得.
     */
    public function getEventTimeZone(): EEventTimeZone
    {
        return $this->eventTimeZone;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getGroupList(): Collection
    {
        return $this->groupList;
    }

    /**
     * 定員に達していないグループを探す.
     */
    public function findAvailableGroup(): ?Group
    {
        return $this->groupList->first(fn (Group $group) => ! $group->isFull());
    }

    /**
     * 保持しているグループにIDを振る.
     */
    public function AssignGroupId(): void
    {
        $this->groupList->each(function (Group $group, int $index) {
            $group->setGroupId($index + 1);
        });
    }
}

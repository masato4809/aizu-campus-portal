<?php

declare(strict_types=1);

namespace App\Console\Commands\Batch\ShuffleLunchMatching;

use App\Enum\App\ShuffleLunch\EGroupType;
use Illuminate\Support\Collection;

class Group
{
    private EGroupType $group = EGroupType::INVALID;

    private int $groupId      = 0;

    /** @var Collection<int, int> */
    private Collection $targetUserIdList;

    public function __construct(EGroupType $group)
    {
        $this->group            = $group;
        $this->groupId          = 0;
        $this->targetUserIdList = new Collection;
    }

    /**
     * グループ種別を取得.
     */
    public function getGroupType(): EGroupType
    {
        return $this->group;
    }

    /**
     * グループIDを取得.
     */
    public function getGroupId(): int
    {
        return $this->groupId;
    }

    /**
     * グループに所属するユーザーIDリストを取得.
     *
     * @return Collection<int, int>
     */
    public function getTargetUserIdList(): Collection
    {
        return $this->targetUserIdList;
    }

    /**
     * グループIDを設定.
     */
    public function setGroupId(int $groupId): void
    {
        $this->groupId = $groupId;
    }

    /**
     * グループがすでに埋まっているかどうか.
     */
    public function isFull(): bool
    {
        return $this->targetUserIdList->count() >= $this->group->value;
    }

    /**
     * グループにユーザーを追加する.
     */
    public function addUser(int $trnUserId): void
    {
        $this->targetUserIdList->add($trnUserId);
    }
}

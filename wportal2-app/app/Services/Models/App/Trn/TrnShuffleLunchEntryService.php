<?php

declare(strict_types=1);

namespace App\Services\Models\App\Trn;

use App\Enum\App\EArchiveLevel;
use App\Enum\App\EPipeKind;
use App\Enum\App\ShuffleLunch\EEventTimeZone;
use App\Facades\Internal\PipeService;
use App\Models\App\Trn\TrnShuffleLunchEntry;
use App\Pipe\App\PipeAppShuffleLunchCancel;
use App\Pipe\App\PipeAppShuffleLunchRegister;
use App\Pipe\PipeBase;
use App\Services\Models\ModelServiceBase;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Throwable;

class TrnShuffleLunchEntryService extends ModelServiceBase
{
    /**
     * パイプによる更新.
     *
     * @throws Throwable
     */
    public function updateByPipe(): void
    {
        foreach (PipeService::getPipes(__CLASS__) as $pipe) {
            $kind = $pipe->getKind();
            match ($kind) {
                EPipeKind::APP_SHUFFLE_LUNCH_REGISTER => $this->execAppShuffleLunchRegister($pipe),
                EPipeKind::APP_SHUFFLE_LUNCH_CANCEL   => $this->execAppShuffleLunchCancel($pipe),
                default                               => throw new Exception,
            };
            $pipe->onUpdate(__CLASS__);
        }
    }

    /**
     * @throws Throwable
     */
    private function execAppShuffleLunchRegister(
        PipeBase $pipe
    ): void {
        /** @var PipeAppShuffleLunchRegister $pipe */
        $new                  = new TrnShuffleLunchEntry;
        $new->trn_user_id     = $pipe->authUser?->TrnUser?->id ?: 0;
        $new->event_date      = Carbon::now();
        $new->event_time_zone = $pipe->eventTimeZone;

        $this->insertOrFail($new);

        $pipe->entryCount     = $this->listToday()->count();
    }

    /**
     * @throws Throwable
     */
    private function execAppShuffleLunchCancel(
        PipeBase $pipe
    ): void {
        /** @var PipeAppShuffleLunchCancel $pipe */

        // 本日のエントリーを取得する.
        $entry                  = $this->findByUserIdInTodayOrFail(
            $pipe->authUser?->TrnUser?->id ?: 0
        );

        // archiveに更新する.
        $entry->e_archive_level = EArchiveLevel::ARCHIVE;
        $this->updateOrFail($entry);
    }

    /**
     * 本日の参加者を取得する.
     *
     * @return Collection<int, TrnShuffleLunchEntry>
     */
    public function listToday(): Collection
    {
        /** @var Collection<int, TrnShuffleLunchEntry> */
        return TrnShuffleLunchEntry::query()
            ->whereDate('event_date', Carbon::now())
            ->alive()
            ->get();
    }

    /**
     * @return Collection<int, TrnShuffleLunchEntry>
     */
    public function listTodayByEventTimeZone(EEventTimeZone $eventTimeZone): Collection
    {
        /** @var Collection<int, TrnShuffleLunchEntry> */
        return TrnShuffleLunchEntry::query()
            ->whereDate('event_date', Carbon::now())
            ->where('event_time_zone', $eventTimeZone)
            ->alive()
            ->get();
    }

    /**
     * ユーザーIDを指定して本日のエントリーを取得する.
     */
    public function findByUserIdInTodayOrFail(
        int $trnUserId
    ): TrnShuffleLunchEntry {
        /** @var TrnShuffleLunchEntry */
        return TrnShuffleLunchEntry::query()
            ->where('trn_user_id', $trnUserId)
            ->whereDate('event_date', Carbon::now())
            ->alive()
            ->firstOrFail();
    }
}

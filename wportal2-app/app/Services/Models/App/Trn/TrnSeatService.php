<?php

declare(strict_types=1);

namespace App\Services\Models\App\Trn;

use App\Enum\App\EArchiveLevel;
use App\Models\App\Trn\TrnSeat;
use App\Services\Models\ModelServiceBase;
use Illuminate\Support\Collection;

class TrnSeatService extends ModelServiceBase
{
    /**
     * 全て取得.
     *
     * @param  array<string>  $with
     * @return Collection<int, TrnSeat>
     */
    public function list(array $with = []): Collection
    {
        /** @var Collection<int, TrnSeat> */
        return TrnSeat::query()
            ->with($with)
            ->where('e_archive_level', EArchiveLevel::ALIVE->value)
            ->get();
    }

    /**
     * IDで検索(orFail).
     *
     * @param  array<mixed>  $with
     */
    public function findByIdOrFail(int $id, array $with = []): TrnSeat
    {
        /** @var TrnSeat */
        return TrnSeat::query()
            ->with($with)
            ->where('e_archive_level', EArchiveLevel::ALIVE->value)
            ->findOrFail($id);
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Models\App\Trn;

use App\Enum\App\EBatchRelease;
use App\Models\App\Trn\TrnBatchReleaseHistory;
use App\Services\Models\ModelServiceBase;

class TrnBatchReleaseHistoryService extends ModelServiceBase
{
    /**
     * 以下・取得関数
     */

    /**
     * batch_identifyによる検索.
     */
    public function findByBatchIdentify(EBatchRelease $eIdentify): ?TrnBatchReleaseHistory
    {
        /** @var TrnBatchReleaseHistory|null */
        return TrnBatchReleaseHistory::query()
            ->where('batch_identify', $eIdentify)
            ->alive()
            ->first();
    }
}

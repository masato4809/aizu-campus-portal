<?php

declare(strict_types=1);

namespace App\Models\App\Trn;

use App\Models\App\ModelBase;
use App\Trait\EagerLoadHelper;

/**
 * @mixin TrnBatchReleaseHistory
 *
 * @extends ModelBase<TrnBatchReleaseHistory>
 */
class TrnBatchReleaseHistory extends ModelBase
{
    use EagerLoadHelper;

    protected $table = 'trn_batch_release_history';
}

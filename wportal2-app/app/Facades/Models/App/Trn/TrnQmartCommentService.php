<?php

declare(strict_types=1);

namespace App\Facades\Models\App\Trn;

use Illuminate\Support\Facades\Facade;

class TrnQmartCommentService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'models\app\trn\trn_qmart_comment_service';
    }
}

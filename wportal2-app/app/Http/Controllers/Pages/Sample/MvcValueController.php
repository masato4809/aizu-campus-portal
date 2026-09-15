<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages\Sample;

use App\Http\Controllers\Controller;
use App\Models\App\Trn\TrnUser;
use Inertia\Response;

class MvcValueController extends Controller
{
    public function invoke(): Response
    {
        // DBからのデータ取得サンプル.
        $userList = TrnUser::query()
            ->limit(10)
            ->get();

        return $this->render('Sample/MvcValue/Index', [
            'numberValue'     => 919,
            'stringValue'     => 'This message was provided from PHP Controller.',
            'arrayValue'      => [
                11,
                22,
                '33',
                44,
            ],

            'trnUserList'     => $userList->toArray(),
        ]);
    }
}

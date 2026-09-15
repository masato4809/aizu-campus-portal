<?php

declare(strict_types=1);

namespace App\Pipe\App;

use App\Enum\App\EPipeKind;
use App\Facades\Models\App\Auth\AuthUserService;
use App\Pipe\PipeBase;

final class PipeAppUserDelete extends PipeBase
{
    public int $trnUserId  = 0;

    public int $authUserId = 0;

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::APP_USER_DELETE, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        $this->trnUserId         = $this->getArgAsInteger('trnUserId');

        // 認証ユーザーID.
        $authUser                = AuthUserService::findByIdOrFail($this->trnUserId);
        $this->authUserId        = $authUser->id;
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void {}
}

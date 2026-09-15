<?php

declare(strict_types=1);

namespace App\Pipe\App;

use App\Enum\App\EPipeKind;
use App\Pipe\PipeBase;

final class PipeAppUserUpdate extends PipeBase
{
    public int $authId               = 0;

    public int $trnUserId            = 0;

    public bool $enableLegacyLogin   = false;

    public string $overwritePassword = '';

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::APP_USER_UPDATE, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        $this->authId            = $this->getArgAsInteger('authId');
        $this->trnUserId         = $this->getArgAsInteger('trnUserId');
        $this->enableLegacyLogin = $this->getArgAsBoolean('enableLegacyLogin');
        $this->overwritePassword = $this->getArgAsString('overwritePassword');
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void {}
}

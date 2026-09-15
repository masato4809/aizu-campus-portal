<?php

declare(strict_types=1);

namespace App\Pipe\App;

use App\Enum\App\EPipeKind;
use App\Pipe\PipeBase;

final class PipeAppOnetimePasswordLogin extends PipeBase
{
    public int $authId             = 0;

    public string $email           = '';

    public string $onetimePassword = '';

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::APP_ONETIME_PASSWORD_LOGIN, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        $this->authId          = $this->getArgAsInteger('authId');
        $this->email           = $this->getArgAsString('email');
        $this->onetimePassword = $this->getArgAsString('onetimePassword');
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void {}
}

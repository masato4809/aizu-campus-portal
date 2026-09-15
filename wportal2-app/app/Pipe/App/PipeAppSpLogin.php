<?php

declare(strict_types=1);

namespace App\Pipe\App;

use App\Enum\App\EPipeKind;
use App\Models\App\Auth\AuthUser;
use App\Pipe\PipeBase;

final class PipeAppSpLogin extends PipeBase
{
    public string $email             = '';

    public string $recaptchaToken    = '';

    public string $password          = '';

    public string $code              = '';

    public ?AuthUser $targetAuthUser = null;

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::APP_SP_LOGIN, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        $this->email          = $this->getArgAsString('email');
        $this->password       = $this->getArgAsString('password');
        $this->recaptchaToken = $this->getArgAsString('recaptchaToken');
        $this->code           = $this->getArgAsString('code');
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void {}
}

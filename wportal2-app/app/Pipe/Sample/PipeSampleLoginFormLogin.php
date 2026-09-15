<?php

declare(strict_types=1);

namespace App\Pipe\Sample;

use App\Enum\App\EPipeKind;
use App\Pipe\PipeBase;

final class PipeSampleLoginFormLogin extends PipeBase
{
    public string $email    = '';

    public string $password = '';

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::SAMPLE_LOGIN_FORM_LOGIN, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        $this->email    = $this->getArgAsString('email');
        $this->password = $this->getArgAsString('password');
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void {}
}

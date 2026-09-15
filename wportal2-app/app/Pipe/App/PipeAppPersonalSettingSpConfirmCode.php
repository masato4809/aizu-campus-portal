<?php

declare(strict_types=1);

namespace App\Pipe\App;

use App\Enum\App\EPipeKind;
use App\Models\App\Auth\AuthUser;
use App\Pipe\PipeBase;
use Illuminate\Support\Facades\Auth;

final class PipeAppPersonalSettingSpConfirmCode extends PipeBase
{
    public string $code        = '';

    public ?AuthUser $authUser = null;

    // 認証が成功したかどうか.
    public bool $isValid       = false;

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::APP_PERSONAL_SETTING_SP_CONFIRM_CODE, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        $this->code     = $this->getArgAsString('code');

        $this->authUser = Auth::user();
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void {}
}

<?php

declare(strict_types=1);

namespace App\Pipe\App;

use App\Enum\App\EPipeKind;
use App\Facades\Models\App\Trn\TrnUserService;
use App\Models\App\Auth\AuthUser;
use App\Models\App\Trn\TrnUser;
use App\Pipe\PipeBase;
use Illuminate\Support\Facades\Auth;

final class PipeAppPersonalSettingShowAlignSlack extends PipeBase
{
    // 認証情報から取得するユーザー情報.
    public int $trnUserId         = 0;

    public string $email          = '';

    public ?string $birthDate     = null;

    // SlackAPIで取得したユーザー情報.
    public string $slackUserId    = '';

    public string $slackUserName  = '';

    public string $slackTeamId    = '';

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::APP_PERSONAL_SETTING_SHOW_ALIGN_SLACK, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        /** @var AuthUser $authUser */
        $authUser        = Auth::user();

        /** @var TrnUser $trnUser */
        $trnUser         = TrnUserService::findByAuthIdOrFail($authUser->id);

        // 認証情報から取得するユーザー情報.
        $this->trnUserId = $trnUser->id;
        $this->email     = $authUser->email;
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void {}
}

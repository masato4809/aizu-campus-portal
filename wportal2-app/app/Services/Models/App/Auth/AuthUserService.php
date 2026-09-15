<?php

declare(strict_types=1);

namespace App\Services\Models\App\Auth;

use App\Enum\App\EArchiveLevel;
use App\Enum\App\EEnableFlag;
use App\Enum\App\EPipeKind;
use App\Facades\Internal\PipeService;
use App\Models\App\Auth\AuthUser;
use App\Pipe\App\PipeAppLegacyLogin;
use App\Pipe\App\PipeAppPersonalSettingSpConfirmCode;
use App\Pipe\App\PipeAppPersonalSettingSpPassword;
use App\Pipe\App\PipeAppUserCreate;
use App\Pipe\App\PipeAppUserDelete;
use App\Pipe\App\PipeAppUserUpdate;
use App\Pipe\PipeBase;
use App\Services\Models\ModelServiceBase;
use Carbon\Carbon;
use Exception;
use PragmaRX\Google2FA\Google2FA;
use Throwable;

class AuthUserService extends ModelServiceBase
{
    /**
     * パイプによる更新.
     *
     * @throws Throwable
     */
    public function updateByPipe(): void
    {
        foreach (PipeService::getPipes(__CLASS__) as $pipe) {
            $kind = $pipe->getKind();
            match ($kind) {
                EPipeKind::APP_USER_CREATE                      => $this->execAppUserCreate($pipe),
                EPipeKind::APP_USER_UPDATE                      => $this->execAppUserUpdate($pipe),
                EPipeKind::APP_USER_DELETE                      => $this->execAppUserDelete($pipe),
                EPipeKind::APP_LEGACY_LOGIN                     => $this->execAppLegacyLogin($pipe),
                EPipeKind::APP_PERSONAL_SETTING_SP_PASSWORD     => $this->execAppPersonalSettingSpPassword($pipe),
                EPipeKind::APP_PERSONAL_SETTING_SP_CONFIRM_CODE => $this->execAppPersonalSettingSpConfirmCode($pipe),
                default                                         => throw new Exception,
            };
            $pipe->onUpdate(__CLASS__);
        }
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    private function execAppUserCreate(
        PipeBase $pipe
    ): void {
        /** @var PipeAppUserCreate $pipe */

        // 新規認証ユーザーを追加.
        $new                   = new AuthUser;
        $new->name             = $pipe->name;
        $new->email            = $pipe->email;
        $new->password         = substr(bin2hex(random_bytes(16)), 0, 16);
        $new->google_id        = '';

        $this->insertOrFail($new);

        // 作成した認証ユーザーをパイプに保存.
        $pipe->createdAuthUser = $new;
    }

    /**
     * @throws Throwable
     */
    private function execAppUserUpdate(
        PipeBase $pipe
    ): void {
        /** @var PipeAppUserUpdate $pipe */
        $authUser                        = $this->findByIdOrFail($pipe->authId);

        // レガシーログイン許可の更新.
        $authUser->e_enable_legacy_login = $pipe->enableLegacyLogin
            ? EEnableFlag::ENABLE->value : EEnableFlag::INVALID->value;

        // 許可かつ空文字でない場合はパスワードの更新.
        if ($pipe->enableLegacyLogin && ! empty($pipe->overwritePassword)) {
            $authUser->password = $pipe->overwritePassword;
        }

        // 更新の実施.
        $this->updateOrFail($authUser);
    }

    /**
     * @throws Exception|Throwable
     */
    private function execAppUserDelete(
        PipeBase $pipe
    ): void {
        /** @var PipeAppUserDelete $pipe */

        // 対象データを取得.
        $target                  = $this->findByIdOrFail($pipe->authUserId);

        // 対象データを削除.
        $target->e_archive_level = EArchiveLevel::ARCHIVE->value;

        // 削除を保存.
        $this->updateOrFail($target);
    }

    /**
     * @throws Exception|Throwable
     */
    private function execAppLegacyLogin(
        PipeBase $pipe
    ): void {
        /** @var PipeAppLegacyLogin $pipe */
        if (is_null($pipe->targetAuthUser)) {
            throw new Exception;
        }

        // ワンタイムパスワードの作成(6桁数値).
        $max                                            = 10** 6 - 1;
        $min                                            = 10** 5;
        $onetimePassword                                = (string) random_int($min, $max);

        // ワンタイムパスワードの有効期限.
        $expired                                        = Carbon::now()->addMinutes(10);

        // ワンタイムパスワード&期限の更新.
        $pipe->targetAuthUser->onetime_password         = $onetimePassword;
        $pipe->targetAuthUser->onetime_password_expired = $expired;
        $this->updateOrFail($pipe->targetAuthUser);
    }

    /**
     * @throws Exception|Throwable
     */
    private function execAppPersonalSettingSpPassword(
        PipeBase $pipe
    ): void {
        /** @var PipeAppPersonalSettingSpPassword $pipe */
        if (is_null($pipe->authUser)) {
            throw new Exception;
        }
        $authUser                       = $pipe->authUser;

        // SPログインの禁止
        $authUser->e_enable_sp_login    = EEnableFlag::INVALID->value;

        // パスワードの更新.
        $authUser->sp_password          = $pipe->password;

        // シークレットの更新.
        $google2fa                      = new Google2FA;
        $authUser->sp_google_2fa_secret = $google2fa->generateSecretKey();

        // DB保存.
        $this->updateOrFail($authUser);

        // URL作成.
        $name                           = isLocal() ? 'wportal2(local)' : 'Web本部ポータル2';
        $pipe->resultUrl                = $google2fa->getQRCodeUrl(
            $name,
            $authUser->email,
            $authUser->sp_google_2fa_secret
        );
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    private function execAppPersonalSettingSpConfirmCode(
        PipeBase $pipe
    ): void {
        /** @var PipeAppPersonalSettingSpConfirmCode $pipe */
        if (is_null($pipe->authUser)) {
            throw new Exception;
        }
        $authUser      = $pipe->authUser;

        // コード.
        $code          = $pipe->code;

        // シークレット.
        $secret        = $authUser->sp_google_2fa_secret;

        // 認証確認.
        $google2fa     = new Google2FA;
        $pipe->isValid = (bool) $google2fa->verifyKey($secret, $code);
        if ($pipe->isValid) {
            // ログイン失敗回数を0に初期化してSPログインの許可.
            $authUser->e_enable_sp_login     = EEnableFlag::ENABLE->value;
            $authUser->sp_login_failed_count = 0;
            $this->updateOrFail($authUser);
        }
    }

    /**
     * IDで検索(orFail).
     *
     * @param  array<mixed>  $with
     */
    public function findByIdOrFail(int $id, array $with = []): AuthUser
    {
        /** @var AuthUser */
        return AuthUser::query()
            ->with($with)
            ->findOrFail($id);
    }
}

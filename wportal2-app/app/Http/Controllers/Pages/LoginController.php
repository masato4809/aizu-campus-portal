<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Constant;
use App\Enum\App\EArchiveLevel;
use App\Enum\App\EEnableFlag;
use App\Enum\App\EPages;
use App\Enum\App\EUserAuthority;
use App\Facades\External\RecaptchaService;
use App\Facades\External\SesService;
use App\Facades\Internal\PipeService;
use App\Facades\Models\App\Auth\AuthUserService;
use App\Facades\Models\App\Trn\TrnUserAuthorityService;
use App\Facades\Models\App\Trn\TrnUserRewardService;
use App\Facades\Models\App\Trn\TrnUserService;
use App\Http\Controllers\Controller;
use App\Models\App\Auth\AuthUser;
use App\Pipe\App\PipeAppLegacyLogin;
use App\Pipe\App\PipeAppOnetimePasswordLogin;
use App\Pipe\App\PipeAppSpLogin;
use App\Services\External\SesService\SesParameter;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Response;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User;
use PragmaRX\Google2FA\Google2FA;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Throwable;

class LoginController extends Controller
{
    public function invoke(): Response
    {
        return $this->render('Login/Index');
    }

    /**
     * ローカル用ログイン.
     *
     * @throws Throwable
     */
    public function guestLogin(): RedirectResponse
    {
        // ローカル専用のrouteで呼ばれるはずがないため.
        if (! isLocal()) {
            abort(404);
        }

        // ゲスト用メールアドレス.
        $email      = 'guest@919.jp';

        // アクティブなユーザー情報を検索.
        /** @var AuthUser|null $authUser */
        $authUser   = AuthUser::query()
            ->where('email', $email)
            ->first();

        // 新規ユーザーの作成.
        if ($authUser === null) {
            // 認証ユーザーを作成.
            $authUser                    = new AuthUser;
            $authUser->email             = $email;
            $authUser->name              = '名字 名前';
            $authUser->email_verified_at = now();
            $authUser->password          = substr(bin2hex(random_bytes(16)), 0, 16);
            $authUser->google_id         = '';
            $authUser->saveOrFail();

            // ユーザーを作成.
            $trnUser                     = TrnUserService::createNewUser($authUser, '名字 名前');

            // ユーザー作成の報酬.
            TrnUserRewardService::createNewUser($trnUser->id);
            
            TrnUserAuthorityService::addAuthority($trnUser->id, EUserAuthority::ROOT_PRIVILEGE);
        }

        Auth::login($authUser, true);

        return redirect('/');
    }

    /**
     * ワンタイムパスワードの発行.
     *
     * @throws Throwable
     */
    public function legacy_login(Request $request): Response
    {
        // パイプ作成.
        /** @var PipeAppLegacyLogin $pipe */
        $pipe                 = PipeService::insertNewPipe(
            new PipeAppLegacyLogin($request->toArray())
        );

        // バリデーションチェック.
        if (! PipeService::isValid()) {
            return $this->render('Login/Index');
        }

        // recaptchaのチェック.
        if (! RecaptchaService::isValidToken($pipe->recaptchaToken)) {
            return $this->render('Login/Index');
        }

        // 一致する認証ユーザーを取得.
        /** @var AuthUser|null $targetAuthUser */
        $targetAuthUser       = AuthUser::query()
            ->where('e_enable_legacy_login', EEnableFlag::ENABLE)
            ->where('email', $pipe->email)
            ->first();
        $pipe->targetAuthUser = $targetAuthUser;

        if (is_null($pipe->targetAuthUser) || ! Hash::check($pipe->password, $pipe->targetAuthUser->password)) {
            return $this->render('Login/Index');
        }

        // 更新処理.
        DB::transaction(function () use ($pipe) {
            // ワンタイムパスワードの更新.
            AuthUserService::updateByPipe();

            // パスワード情報の送信.
            $this->sendOnetimePassword(
                $pipe->targetAuthUser->email,
                $pipe->targetAuthUser->onetime_password,
            );
        });

        // ワンタイムパスワード入力ページをPost経由のまま表示.
        return $this->render('Login/OnetimePasswordIndex', [
            'email'  => $pipe->targetAuthUser->email,
            'authId' => $pipe->targetAuthUser->id,
        ]);
    }

    /**
     * ワンタイムパスワードによるログイン
     */
    public function onetime_password_login(Request $request): RedirectResponse
    {
        // パイプ作成.
        /** @var PipeAppOnetimePasswordLogin $pipe */
        $pipe     = PipeService::insertNewPipe(
            new PipeAppOnetimePasswordLogin($request->toArray())
        );

        // id,メルアド,パスワードが一致するレガシーログイン可能な認証ユーザーを確認.
        /** @var AuthUser|null $authUser */
        $authUser = AuthUser::query()
            ->where('id', $pipe->authId)
            ->where('email', $pipe->email)
            ->where('e_enable_legacy_login', EEnableFlag::ENABLE)
            ->where('onetime_password', $pipe->onetimePassword)
            ->where('onetime_password_expired', '>', Carbon::now())
            ->first();

        // 確認できない場合はログ出力してログイン画面へ.
        if (is_null($authUser)) {
            Log::error("invalid onetime password id:$pipe->authId email:$pipe->email pass:$pipe->onetimePassword");

            return redirect()->action(EPages::LOGIN->getInvokePath());
        }

        // ログイン処理を実施
        Auth::login($authUser, true);

        return redirect()->action(EPages::DASHBOARD->getInvokePath());
    }

    /**
     * SP版ログインの実施.
     */
    public function sp_login(Request $request): RedirectResponse
    {
        // パイプ作成.
        /** @var PipeAppSpLogin $pipe */
        $pipe                 = PipeService::insertNewPipe(
            new PipeAppSpLogin($request->toArray())
        );

        // バリデーションチェック.
        if (! PipeService::isValid()) {
            redirect()->action(EPages::LOGIN->getInvokePath());
        }

        // recaptchaのチェック.
        if (! RecaptchaService::isValidToken($pipe->recaptchaToken)) {
            redirect()->action(EPages::LOGIN->getInvokePath());
        }

        // 許可&パスワードが一致し、不正カウンタが問題ない認証ユーザーを取得.
        /** @var AuthUser|null $targetAuthUser */
        $targetAuthUser       = AuthUser::query()
            ->where('e_enable_sp_login', EEnableFlag::ENABLE)
            ->where('email', $pipe->email)
            ->where('sp_login_failed_count', '<', Constant::INVALID_SP_LOGIN_COUNT)
            ->first();
        $pipe->targetAuthUser = $targetAuthUser;

        // 対象が見つからない場合.
        if (is_null($pipe->targetAuthUser)) {
            return redirect()->action(EPages::LOGIN->getInvokePath());
        }

        // パスワードが違う場合.
        if (! Hash::check($pipe->password, $pipe->targetAuthUser->sp_password)) {
            Log::error("invalid password sp login, mail: [$pipe->email]");

            return redirect()->action(EPages::LOGIN->getInvokePath());
        }

        // tokenコードのチェック.
        try {
            $google2fa   = new Google2FA;
            $isValidCode = $google2fa->verifyKey($pipe->targetAuthUser->sp_google_2fa_secret, $pipe->code);
            if (! $isValidCode) {
                // 不正アクセスカウントを加算.
                $pipe->targetAuthUser->sp_login_failed_count++;
                $pipe->targetAuthUser->saveOrFail();

                Log::error("invalid authenticator code, mail: [$pipe->email]");

                return redirect()->action(EPages::LOGIN->getInvokePath());
            }

            // ログイン処理を実施
            Auth::login($pipe->targetAuthUser, true);

            return redirect()->action(EPages::DASHBOARD->getInvokePath());
        } catch (Exception $e) {
            Log::error('invalid code:'.$e->getMessage());

            return redirect()->action(EPages::LOGIN->getInvokePath());
        }
    }

    /**
     * google認証の実施.
     */
    public function getGoogleAuth(): RedirectResponse
    {
        return Socialite::driver('google')
            ->redirect();
    }

    /**
     * @throws Throwable
     */
    public function authGoogleCallback(): object
    {
        /** @var User $googleUser */
        $googleUser = Socialite::driver('google')->stateless()->user();

        // アクティブなユーザー情報を検索.
        /** @var AuthUser|null $authUser */
        $authUser   = AuthUser::query()
            ->where('email', $googleUser->email)
            ->alive()
            ->first();

        // ユーザーがなく、919ドメインでなければ止めたいログイン.
        if ($authUser === null && ! $this->is919Domain($googleUser)) {
            Log::error('invalid domain:'.$googleUser->email);

            return redirect('/login');
        }

        // 新規ユーザーの作成.
        if ($authUser === null) {
            $authUser = $this->createNewUserByGoogle($googleUser);
        }

        Auth::login($authUser, true);

        return redirect('/dashboard');
    }

    /**
     * 919ドメインかどうか.
     */
    private function is919Domain(User $googleUser): bool
    {
        // 919ドメインチェックが無効の場合は919ドメインとして扱う.
        $check919Domain = config('auth.check919Domain');
        if (! $check919Domain) {
            return true;
        }

        // 認証アドレスが919.jpのみ.
        $hd             = $googleUser->user['hd'] ?? '';
        if ($hd === '919.jp') {
            return true;
        }

        return false;
    }

    /**
     * ログアウト処理.
     */
    public function logout(): object
    {
        Auth::logout();

        return redirect('/login');
    }

    /**
     * 新規ユーザーの作成.
     *
     *
     * @throws Throwable
     */
    private function createNewUserByGoogle(User $googleUser): AuthUser
    {
        // アーカイブデータの検索.
        /** @var AuthUser|null $archiveAuthUser */
        $archiveAuthUser             = AuthUser::query()
            ->where('email', $googleUser->email)
            ->whereNot('e_archive_level', EArchiveLevel::ALIVE)
            ->first();

        // もしアーカイブデータがあれば復帰する.
        if (! is_null($archiveAuthUser)) {
            DB::transaction(function () use ($archiveAuthUser) {
                // 認証ユーザーを復帰.
                $archiveAuthUser->e_archive_level = EArchiveLevel::ALIVE;
                $archiveAuthUser->saveOrFail();

                // TrnUserを復帰.
                TrnUserService::comebackUser($archiveAuthUser);
            });

            return $archiveAuthUser;
        }

        // 新規作成の実施.
        return DB::transaction(static function () use ($googleUser) {
            $name                        = $googleUser->user['family_name'].' '.$googleUser->user['given_name'];

            // 認証ユーザーを作成.
            $authUser                    = new AuthUser;
            $authUser->email             = $googleUser->getEmail();
            $authUser->name              = $name;
            $authUser->email_verified_at = now();
            $authUser->password          = substr(bin2hex(random_bytes(16)), 0, 16);
            $authUser->google_id         = $googleUser->getId();
            $authUser->saveOrFail();

            // ユーザーを作成.
            $trnUser                     = TrnUserService::createNewUser($authUser, $name);

            // ユーザー作成の報酬.
            TrnUserRewardService::createNewUser($trnUser->id);

            return $authUser;
        });
    }

    /**
     * ワンタイムパスワード情報を送信
     */
    private function sendOnetimePassword(string $to, string $password): void
    {
        // タイトルの作成.
        $subject = '【Web本部ポータル】ワンタイムパスワードのお知らせ';

        // 本文の作成.
        $message = <<< EOF
以下のワンタイムパスワードを10分以内に入力してください。

[$password]

※本メールは送信専用です。返信はできません。
※本メールにお心当たりのない場合は、管理者までご連絡お願いいたします。
管理者連絡先 <miki-yoji@919.jp>
EOF;

        // 送信パラメータの作成.
        $param   = new SesParameter;
        $param->setFrom('no-reply@wportal.org');
        $param->setTo($to);
        $param->setSubject($subject);
        $param->setMessageBody($message);

        // 送信の実施.
        SesService::sendMail($param);
    }
}

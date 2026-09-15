<?php

declare(strict_types=1);

namespace App\Services\External;

/**
 * Recaptchaに対する処理.
 */
class RecaptchaService
{
    /**
     * recaptchaから受信したtokenが有効かどうかチェックする.
     */
    public function isValidToken(string $recaptchaToken): bool
    {
        // シークレットキーは環境変数から取得する.
        $secretKey         = (string) config('recaptcha.SecretKey');

        // URLを取得.
        $verifyUrl         = (string) config('recaptcha.VerifyUrl');

        // recaptchaにtokenを問い合わせる.
        $url               = "$verifyUrl?secret=$secretKey&response=$recaptchaToken";
        $verifyResponse    = file_get_contents($url);
        if ($verifyResponse === false) {
            return false;
        }

        /** @var object{success: bool} $recaptchaResponse */
        $recaptchaResponse = json_decode($verifyResponse);

        // 結果を判定.
        $result            = $recaptchaResponse->success;
        if ($result === false) {
            return false;
        }

        return true;
    }
}

<?php

declare(strict_types=1);

/**
 * Recaptchaの設定
 */
return [
    // secret key.
    'SecretKey' => env('RECAPTCHA_SECRET_KEY', ''),

    // verify url.
    'VerifyUrl' => 'https://www.google.com/recaptcha/api/siteverify',
];

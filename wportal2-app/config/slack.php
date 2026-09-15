<?php

declare(strict_types=1);

/**
 * Slackの設定
 */

return [
    // app token.
    'AppToken'          => env('SLACK_APP_TOKEN', ''),

    // bot user oauth token.
    'BotUserOAuthToken' => env('SLACK_BOT_USER_OAUTH_TOKEN', ''),

    // notice webhook url.
    'NoticeUrl'         => env('SLACK_URL_NOTICE', ''),

    // error webhook url.
    'ErrorUrl'          => env('SLACK_URL_ERROR', ''),
];

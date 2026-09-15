<?php

declare(strict_types=1);

namespace App\Trait;

/**
 * Slackで利用するAPIを定義する.
 */
trait SlackApi
{
    /**
     * BotTokenを取得.
     */
    private function getBotToken(): string
    {
        return (string) config('slack.BotUserOAuthToken');
    }

    /**
     * https://api.slack.com/methods/users.lookupByEmail
     */
    private function slackApiLookupByEmail(string $emailAddress): bool|string
    {
        $params  = [
            // メッセージ本文.
            'token'   => $this->getBotToken(),
            'email'   => $emailAddress,
        ];

        // 送信オプション.
        $options = [
            CURLOPT_URL            => 'https://slack.com/api/users.lookupByEmail',
            CURLOPT_HTTPHEADER     => ['Content-type: application/x-www-form-urlencoded'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST           => false,
            CURLOPT_POSTFIELDS     => http_build_query($params),
        ];

        return $this->executeCurl($options);
    }

    /**
     * https://api.slack.com/methods/chat.postMessage
     */
    private function slackApiPostMessage(string $channelId, string $message): bool|string
    {
        $params  = [
            'channel' => $channelId,
            'text'    => $message,
        ];

        $headers = [
            'Authorization: Bearer '.$this->getBotToken(),
            'Content-Type: application/json;charset=utf-8',
        ];

        // 送信オプション.
        $options = [
            CURLOPT_URL            => 'https://slack.com/api/chat.postMessage',
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($params),
        ];

        // curlで送信
        return $this->executeCurl($options);
    }

    /**
     * curlを実行
     *
     * @param  array<mixed>  $options
     */
    private function executeCurl(array $options): bool|string
    {
        // curlで送信
        $curl   = curl_init();
        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }
}

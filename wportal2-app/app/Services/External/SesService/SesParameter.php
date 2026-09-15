<?php

declare(strict_types=1);

namespace App\Services\External\SesService;

use function count;

/**
 * SES送信用パラメータ.
 */
class SesParameter
{
    // 送信元
    private string $from        = '';

    // 送信先.
    /** @var array<string> */
    private array $toArray      = [];

    // 題名.
    private string $subject     = '';

    // 本文.
    private string $messageBody = '';

    public function isValid(): bool
    {
        // 送信元のチェック.
        if (empty($this->from)) {
            return false;
        }

        // 送信先のチェック.
        if (count($this->toArray) === 0) {
            return false;
        }

        return true;
    }

    /**
     * 送信用パラメータの取得.
     *
     * @return array<mixed>
     */
    public function getParameterArray(): array
    {
        if (! $this->isValid()) {
            return [];
        }

        return [
            'Source'      => $this->from,
            'Destination' => [
                'ToAddresses' => $this->toArray,
            ],
            'Message'     => [
                'Subject' => [
                    'Charset' => $this->getCharset(),
                    'Data'    => $this->subject,
                ],
                'Body'    => [
                    'Text' => [
                        'Charset' => $this->getCharset(),
                        'Data'    => $this->messageBody,
                    ],
                ],
            ],
        ];
    }

    /**
     * 送信元の設定.
     */
    public function setFrom(string $fromAddress): void
    {
        $this->from = $fromAddress;
    }

    /**
     * 送信先の設定（単独）
     */
    public function setTo(string $to): void
    {
        $this->toArray   = [];
        $this->toArray[] = $to;
    }

    /**
     * 送信先の設定（複数）
     *
     * @param  array<string>  $toArray
     */
    public function setToArray(array $toArray): void
    {
        $this->toArray = $toArray;
    }

    /**
     * 題名の設定.
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * 本文の設定.
     */
    public function setMessageBody(string $message): void
    {
        $this->messageBody = $message;
    }

    /**
     * 送信文字コードの取得.
     */
    public function getCharset(): string
    {
        return 'UTF-8';
    }
}

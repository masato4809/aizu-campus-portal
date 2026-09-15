<?php

declare(strict_types=1);

namespace App\Console\Commands\Tool\MakeTypescriptEnum;

final class EnumValue
{
    public string $key     = '';

    public string $value   = '';

    public string $comment = '';

    /**
     * コンストラクタ.
     */
    public function __construct(string $key, string $value, string $comment)
    {
        $this->key     = $key;
        $this->value   = $value;
        $this->comment = $comment;
    }

    /**
     * 出力用のラインに整形する.
     */
    public function toTypescriptLine(): string
    {
        return "  $this->key = $this->value, // $this->comment";
    }
}

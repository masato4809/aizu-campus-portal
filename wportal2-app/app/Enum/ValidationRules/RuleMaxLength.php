<?php

declare(strict_types=1);

namespace App\Enum\ValidationRules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RuleMaxLength implements ValidationRule
{
    public int $max = 0;

    /**
     * New a new rule instance.
     *
     * @return void
     */
    public function __construct(int $max)
    {
        $this->max = $max;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // string以外は通さない.
        if (! is_string($value)) {
            $fail($this->message());
        }

        // 改行コードを統一する.
        $replacedValue = str_replace("\r\n", "\n", (string) $value);

        if (mb_strlen($replacedValue) > $this->max) {
            $fail($this->message());
        }
    }

    /**
     * エラー発生時のmessage.
     */
    private function message(): string
    {
        return ":attributeは{$this->max}文字以下で入力してください。";
    }
}

<?php

declare(strict_types=1);

namespace App\Enum\ValidationRules;

use App\Enum\App\EValidationType;
use Illuminate\Support\Collection;

/**
 * バリデーションルール指定情報.
 */
final class ValidationRuleSet
{
    public EValidationType $type = EValidationType::INVALID;

    public int $value1           = 0;

    public int $value2           = 0;

    public function __construct(
        EValidationType $type,
        int $value1,
        int $value2
    ) {
        $this->type   = $type;
        $this->value1 = $value1;
        $this->value2 = $value2;
    }

    /**
     * パック情報からルールを取得.
     *
     * @return Collection<int, mixed>
     */
    public function rule(): Collection
    {
        return $this->type->getRule($this);
    }

    /**
     * パック情報からメッセージを取得.
     */
    public function message(string $columnName): string
    {
        return $this->type->getErrorMessage(
            $columnName,
            $this
        );
    }
}

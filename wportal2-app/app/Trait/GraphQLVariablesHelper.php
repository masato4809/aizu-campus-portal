<?php

declare(strict_types=1);

namespace App\Trait;

trait GraphQLVariablesHelper
{
    /** @var array<mixed> */
    protected array $args = [];

    /**
     * argumentsから数値の引数を取得.
     *
     * @param  array<mixed>|null  $args
     */
    protected function getArgAsInteger(string $key, ?array $args = null): int
    {
        if ($args === null) {
            $args = $this->args;
        }

        return (int) ($args[$key] ?? 0);
    }

    /**
     * argumentsから文字列の引数を取得
     *
     * @param  array<mixed>|null  $args
     */
    protected function getArgAsString(string $key, ?array $args = null): string
    {
        if ($args === null) {
            $args = $this->args;
        }

        return (string) ($args[$key] ?? '');
    }

    /**
     * argumentsから真偽値の引数を取得
     *
     * @param  array<mixed>|null  $args
     */
    protected function getArgAsBoolean(string $key, ?array $args = null): bool
    {
        if ($args === null) {
            $args = $this->args;
        }

        return (bool) ($args[$key] ?? false);
    }

    /**
     * argumentsから数値の引数を取得.
     *
     * @param  array<mixed>|null  $args
     * @return array<int>
     */
    protected function getArgAsIntArray(string $key, ?array $args = null): array
    {
        if ($args === null) {
            $args = $this->args;
        }

        /** @var callable $callable */
        $callable = 'intval';

        return array_map($callable, (array) $args[$key]);
    }

    /**
     * argumentsからarrayを取得.
     */

    /**
     * argumentsから文字列の引数を取得.
     *
     * @param  array<mixed>|null  $args
     * @return array<string>
     */
    protected function getArgAsStringArray(string $key, ?array $args = null): array
    {
        if ($args === null) {
            $args = $this->args;
        }

        /** @var callable $callable */
        $callable = 'strval';

        return array_map($callable, (array) $args[$key]);
    }
}

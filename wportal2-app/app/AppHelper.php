<?php

declare(strict_types=1);

if (! function_exists('isLocal')) {
    /**
     * ローカル環境かどうか
     */
    function isLocal(): bool
    {
        return config('app.env') === 'local';
    }
}

if (! function_exists('isDev')) {
    /**
     * Dev環境かどうか
     */
    function isDev(): bool
    {
        return config('app.env') === 'dev';
    }
}

if (! function_exists('isStg')) {
    /**
     * Stg環境かどうか
     */
    function isStg(): bool
    {
        return config('app.env') === 'stg';
    }
}

if (! function_exists('isPrd')) {
    /**
     * Prd環境かどうか.
     */
    function isPrd(): bool
    {
        return config('app.env') === 'prd';
    }
}

if (! function_exists('isTst')) {
    /**
     * Tst環境かどうか.
     */
    function isTst(): bool
    {
        return config('app.env') === 'tst';
    }
}

if (! function_exists('localSleep')) {
    /**
     * ローカルのみ偽装遅延させる.
     */
    function localSleep(int $second = 1): void
    {
        if (isLocal()) {
            sleep($second);
        }
    }
}

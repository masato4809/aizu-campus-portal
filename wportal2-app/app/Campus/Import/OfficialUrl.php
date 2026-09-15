<?php

declare(strict_types=1);

namespace App\Campus\Import;

use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use RuntimeException;

class OfficialUrl
{
    public static function resolve(string $base, string $relative): string
    {
        return (string) UriResolver::resolve(new Uri($base), new Uri(html_entity_decode(trim($relative))));
    }

    public static function assertAllowed(string $url): void
    {
        $parts = parse_url($url);
        if (! $parts || ($parts['scheme'] ?? '') !== 'https' || ! in_array($parts['host'] ?? '', ['u-aizu.ac.jp', 'www.u-aizu.ac.jp', 'web-ext.u-aizu.ac.jp'], true)
                     || isset($parts['user']) || isset($parts['pass']) || (isset($parts['port']) && $parts['port'] !== 443)) {
            throw new RuntimeException('Non-official or unsafe fetch URL: '.$url);
        }
    }

    public static function teacherId(string $url): ?string
    {
        if (! preg_match('~/research/faculty/detail$~', (string) parse_url($url, PHP_URL_PATH))) {
            return null;
        }
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        return isset($query['cd']) && is_string($query['cd']) && preg_match('/^\d+$/', $query['cd']) ? $query['cd'] : null;
    }
}

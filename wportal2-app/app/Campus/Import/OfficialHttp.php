<?php

declare(strict_types=1);

namespace App\Campus\Import;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class OfficialHttp
{
    /** @var array<string, string> */
    private array $failures    = [];

    private float $lastRequest = 0;

    /** @var array<string, string> */
    private array $memory      = [];

    public function __construct(private int $delayMs = 750, private bool $refresh = false) {}

    public function get(string $url): string
    {
        $key = explode('#', $url)[0];
        if (isset($this->failures[$key])) {
            throw new RuntimeException($this->failures[$key]);
        }
        try {
            return $this->fetch($url);
        } catch (RuntimeException $error) {
            $this->failures[$key] = $error->getMessage();
            throw $error;
        }
    }

    private function fetch(string $url): string
    {
        $url       = explode('#', $url)[0];
        OfficialUrl::assertAllowed($url);
        if (isset($this->memory[$url])) {
            return $this->memory[$url];
        }
        $directory = app()->runningUnitTests() ? storage_path('framework/testing/u-aizu-cache') : storage_path('app/u-aizu-cache');
        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException('Cannot create import cache');
        }
        $path      = $directory.'/'.hash('sha256', $url).'.html';
        if (! $this->refresh && is_file($path) && filemtime($path) > time() - 86400) {
            return $this->memory[$url] = (string) file_get_contents($path);
        }
        $target    = $url;
        for ($redirect = 0; $redirect < 5; $redirect++) {
            OfficialUrl::assertAllowed($target);
            $response                  = null;
            for ($attempt = 0; $attempt < 2; $attempt++) {
                $wait              = max(0, $this->delayMs / 1000 - (microtime(true) - $this->lastRequest));
                if ($wait > 0) {
                    usleep((int) ($wait * 1000000));
                }
                $this->lastRequest = microtime(true);
                try {
                    $response = Http::withHeaders(['User-Agent' => 'CampusLink-AizuCatalog/1.0 (academic catalog import)', 'Accept' => 'text/html'])
                        ->connectTimeout(10)->timeout(25)->withoutRedirecting()->get($target);
                    if ($response->status() !== 429 && $response->status() < 500) {
                        break;
                    }
                } catch (Throwable $error) {
                    if ($attempt === 1) {
                        throw new RuntimeException('HTTP connection failed: '.$target, 0, $error);
                    }
                }
                usleep(1500000);
            }
            if ($response === null) {
                throw new RuntimeException('HTTP failed: '.$target);
            }
            if (in_array($response->status(), [301, 302, 303, 307, 308], true)) {
                $target = OfficialUrl::resolve($target, $response->header('Location'));

                continue;
            }
            if (! $response->successful()) {
                throw new RuntimeException('HTTP '.$response->status().': '.$target);
            }
            $body                      = $response->body();
            if (preg_match('/charset\s*=\s*["\']?([A-Za-z0-9_-]+)/i', $response->header('Content-Type').' '.substr($body, 0, 1500), $encoding)
                && strcasecmp($encoding[1], 'UTF-8') !== 0 && in_array(strtoupper($encoding[1]), ['SHIFT_JIS', 'SJIS', 'EUC-JP'], true)) {
                $body = (string) mb_convert_encoding($body, 'UTF-8', $encoding[1]);
            }
            if (strlen($body) > 8000000) {
                throw new RuntimeException('HTML exceeds 8 MB: '.$target);
            }
            if (file_put_contents($path, $body, LOCK_EX) === false) {
                throw new RuntimeException('Cannot write import cache');
            }

            return $this->memory[$url] = $body;
        }
        throw new RuntimeException('Too many redirects: '.$url);
    }
}

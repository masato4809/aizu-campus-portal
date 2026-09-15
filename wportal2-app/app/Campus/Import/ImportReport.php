<?php

declare(strict_types=1);

namespace App\Campus\Import;

use RuntimeException;

class ImportReport
{
    /** @var array<string, int> */
    public array $counts = ['Courses found' => 0, 'Courses inserted' => 0, 'Courses updated' => 0, 'Teachers found' => 0, 'Teachers inserted' => 0, 'Teachers updated' => 0, 'Course-teacher relations' => 0, 'Teachers matched' => 0, 'Teachers unmatched' => 0, 'Errors' => 0];

    public readonly string $path;

    public function __construct()
    {
        $this->path = storage_path('logs/u-aizu-import-'.date('Ymd-His').'-'.bin2hex(random_bytes(3)).'.jsonl');
    }

    /** @param array<string, mixed> $context */
    public function event(string $type, array $context): void
    {
        if ($type === 'error') {
            $this->counts['Errors']++;
        }
        if (file_put_contents($this->path, json_encode(['time' => now()->toIso8601String(), 'type' => $type] + $context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n", FILE_APPEND | LOCK_EX) === false) {
            throw new RuntimeException('Cannot write import report');
        }
    }
}

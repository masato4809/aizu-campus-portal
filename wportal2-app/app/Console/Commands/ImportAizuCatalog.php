<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Campus\Import\AizuImporter;
use App\Campus\Import\ImportReport;
use App\Campus\Import\OfficialHttp;
use Illuminate\Console\Command;
use Throwable;

class ImportAizuCatalog extends Command
{
    protected $signature   = 'campus:import-u-aizu {--year=2026} {--dry-run : Parse and match without database writes} {--refresh : Ignore the 24-hour HTTP cache} {--delay=750 : Minimum request interval in milliseconds (250 or more)} {--limit=0 : Limit course codes for a smoke test; 0 imports all}';

    protected $description = 'Import University of Aizu official 2026 courses and faculty';

    public function handle(AizuImporter $importer): int
    {
        if ((string) $this->option('year') !== '2026') {
            $this->error('Only academic year 2026 is supported.');

            return self::FAILURE;
        }
        $delay  = filter_var($this->option('delay'), FILTER_VALIDATE_INT);
        $limit  = filter_var($this->option('limit'), FILTER_VALIDATE_INT);
        if ($delay === false || $delay < 250 || $delay > 10000 || $limit === false || $limit < 0) {
            $this->error('Use --delay=250..10000 and --limit=0 or a positive integer.');

            return self::FAILURE;
        }
        $lock   = fopen(storage_path('logs/u-aizu-import.lock'), 'c');
        if ($lock === false || ! flock($lock, LOCK_EX | LOCK_NB)) {
            $this->error('Another import is running or the lock cannot be opened.');

            return self::FAILURE;
        }
        $report = new ImportReport;
        try {
            $this->info('Reading official 2026 undergraduate and graduate syllabi, then faculty profiles...');
            $importer->run(new OfficialHttp($delay, (bool) $this->option('refresh')), $report, (bool) $this->option('dry-run'), $limit,
                function (string $message) {
                    $this->line($message);
                });
            $this->info('University of Aizu 2026 import completed'.($this->option('dry-run') ? ' (DRY RUN: no DB writes)' : ''));
            foreach ($report->counts as $label => $value) {
                $this->line($label.': '.$value);
            }
            $this->line('Report: '.$report->path);

            return $report->counts['Errors'] > 0 || $report->counts['Courses found'] === 0 ? self::FAILURE : self::SUCCESS;
        } catch (Throwable $error) {
            $report->event('error', ['message' => $error->getMessage()]);
            $this->error($error->getMessage());
            $this->line('Report: '.$report->path);

            return self::FAILURE;
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }
}

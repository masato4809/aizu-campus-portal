<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Campus\Export\AizuCsvExporter;
use Illuminate\Console\Command;
use Throwable;

class ExportAizuCsv extends Command
{
    protected $signature   = 'campus:export-aizu-csv {--year=2026 : Academic year to export}';

    protected $description = 'Export saved University of Aizu catalog and data quality issues as UTF-8 CSV';

    public function handle(AizuCsvExporter $exporter): int
    {
        $year = filter_var($this->option('year'), FILTER_VALIDATE_INT);
        if ($year === false || $year < 1900 || $year > 9999) {
            $this->error('Specify an academic year between 1900 and 9999.');

            return self::FAILURE;
        }
        try {
            $result = $exporter->export($year, storage_path('app/campus-export/'.$year));
            $this->info('University of Aizu CSV export completed.');
            $this->line('Academic year: '.$year);
            foreach (['courses' => 'Courses', 'teachers' => 'Teachers', 'relations' => 'Course-teacher relations', 'catalog' => 'Catalog rows', 'issues' => 'Issues'] as $key => $label) {
                $this->line($label.': '.$result[$key]);
            }
            $this->line('Files:');
            foreach ($result['files'] as $file) {
                $this->line($file);
            }

            return self::SUCCESS;
        } catch (Throwable $error) {
            $this->error('CSV export failed: '.$error->getMessage());

            return self::FAILURE;
        }
    }
}

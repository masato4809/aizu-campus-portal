<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ExceptionBase extends Exception
{
    protected function getNotifyFilePath(): string
    {
        $filePath = str_replace(app_path(), '', $this->file);
        $line     = $this->line;

        return "$filePath:$line";
    }
}

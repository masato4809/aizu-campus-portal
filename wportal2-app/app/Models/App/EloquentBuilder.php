<?php

declare(strict_types=1);

namespace App\Models\App;

use App\Enum\App\EArchiveLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * EloquentBuilder拡張.
 *
 * @extends Builder<Model>
 */
class EloquentBuilder extends Builder
{
    public function alive(): self
    {
        return $this->where('e_archive_level', EArchiveLevel::ALIVE);
    }

    /**
     * 対象をアーカイブにする.
     */
    public function archive(): void
    {
        $this->update([
            'e_archive_level' => EArchiveLevel::ARCHIVE,
        ]);
    }
}

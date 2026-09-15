<?php

declare(strict_types=1);

namespace App\Models\App;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 */
class ModelBase extends Model
{
    /**
     * EloquentBuilderの拡張を行う.
     */
    public function newEloquentBuilder($query): EloquentBuilder
    {
        return new EloquentBuilder($query);
    }
}

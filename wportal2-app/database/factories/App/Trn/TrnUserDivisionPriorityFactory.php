<?php

declare(strict_types=1);

namespace Database\Factories\App\Trn;

use App\Models\App\Trn\TrnUserDivisionPriority;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrnUserDivisionPriority>
 */
class TrnUserDivisionPriorityFactory extends Factory
{
    protected $model = TrnUserDivisionPriority::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'division_priority' => 1,
        ];
    }
}

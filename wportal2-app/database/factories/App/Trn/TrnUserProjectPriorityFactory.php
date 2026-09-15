<?php

declare(strict_types=1);

namespace Database\Factories\App\Trn;

use App\Models\App\Trn\TrnUserProjectPriority;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrnUserProjectPriority>
 */
class TrnUserProjectPriorityFactory extends Factory
{
    protected $model = TrnUserProjectPriority::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'project_priority' => $this->faker->numberBetween(1, 5),
        ];
    }
}

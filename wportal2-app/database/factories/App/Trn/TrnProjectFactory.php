<?php

declare(strict_types=1);

namespace Database\Factories\App\Trn;

use App\Models\App\Trn\TrnProject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrnProject>
 */
class TrnProjectFactory extends Factory
{
    protected $model = TrnProject::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name'    => $this->faker->colorName(),
            'explain' => $this->faker->realText(50),
        ];
    }
}

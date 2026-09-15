<?php

declare(strict_types=1);

namespace Database\Factories\App\Trn;

use App\Models\App\Trn\TrnDivision;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrnDivision>
 */
class TrnDivisionFactory extends Factory
{
    protected $model = TrnDivision::class;

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

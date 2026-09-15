<?php

declare(strict_types=1);

namespace Database\Factories\App\Trn;

use App\Models\App\Trn\TrnDivisionUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrnDivisionUser>
 */
class TrnDivisionUserFactory extends Factory
{
    protected $model = TrnDivisionUser::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'trn_division_id' => $this->faker->numberBetween(1, 10),
            'trn_user_id'     => $this->faker->numberBetween(1, 10),
        ];
    }
}

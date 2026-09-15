<?php

declare(strict_types=1);

namespace Database\Factories\App\Trn;

use App\Models\App\Trn\TrnProjectUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrnProjectUser>
 */
class TrnProjectUserFactory extends Factory
{
    protected $model = TrnProjectUser::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'trn_project_id'  => $this->faker->numberBetween(1, 10),
            'trn_user_id'     => $this->faker->numberBetween(1, 10),
        ];
    }
}

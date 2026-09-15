<?php

declare(strict_types=1);

namespace Database\Factories\App\Trn;

use App\Enum\App\EArchiveLevel;
use App\Models\App\Trn\TrnSeat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrnSeat>
 */
class TrnSeatFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TrnSeat>
     */
    protected $model = TrnSeat::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label'        => $this->faker->regexify('[A-Z][0-9]{1,2}'),
            'position_x'   => $this->faker->numberBetween(-500, 500),
            'position_y'   => $this->faker->numberBetween(-500, 500),
            'phone_number' => $this->faker->optional(0.7)->phoneNumber(),
            'e_archive_level' => EArchiveLevel::ALIVE->value,
        ];
    }
}

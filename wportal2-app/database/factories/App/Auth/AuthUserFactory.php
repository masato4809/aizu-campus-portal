<?php

declare(strict_types=1);

namespace Database\Factories\App\Auth;

use App\Models\App\Auth\AuthUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuthUser>
 */
class AuthUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email_verified_at' => now(),
            'name'              => $this->faker->name,
        ];
    }
}

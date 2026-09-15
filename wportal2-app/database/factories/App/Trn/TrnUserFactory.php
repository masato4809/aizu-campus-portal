<?php

declare(strict_types=1);

namespace Database\Factories\App\Trn;

use App\Models\App\Trn\TrnUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrnUser>
 */
class TrnUserFactory extends Factory
{
    protected $model = TrnUser::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'auth_id'         => 0,
            'nickname'        => $this->faker->name,
            'face_image_path' => 'personal_setting/face/1.jpg',
        ];
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories\App\Trn;

use App\Enum\App\GoodJob\ETargetType;
use App\Models\App\Trn\TrnGoodJob;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrnGoodJob>
 */
class TrnGoodJobFactory extends Factory
{
    protected $model = TrnGoodJob::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'from_target_type'     => ETargetType::INVALID->value,
            'from_trn_user_id'     => 0,
            'from_trn_division_id' => 0,
            'from_trn_project_id'  => 0,
            'from_other_label'     => '',

            'to_target_type'       => ETargetType::INVALID->value,
            'to_trn_user_id'       => 0,
            'to_trn_division_id'   => 0,
            'to_trn_project_id'    => 0,
            'to_other_label'       => '',
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Models\App\Trn;

use App\Models\App\ModelBase;
use App\Trait\EagerLoadHelper;
use Database\Factories\App\Trn\TrnDivisionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin TrnDivision
 *
 * @extends ModelBase<TrnDivision>
 */
class TrnDivision extends ModelBase
{
    /** @use HasFactory<TrnDivisionFactory> */
    use EagerLoadHelper, hasFactory;

    protected $table = 'trn_division';

    /**
     * リレーション:TrnDivisionUser
     *
     * @return HasMany<TrnDivisionUser, covariant TrnDivision>
     */
    public function TrnDivisionUser(): HasMany
    {
        return $this->hasMany(
            TrnDivisionUser::class,
            'trn_division_id',
            'id'
        )->alive();
    }

    /**
     * リレーション:TrnUserDivisionPriority
     *
     * @return HasMany<TrnUserDivisionPriority, covariant TrnDivision>
     */
    public function TrnUserDivisionPriority(): HasMany
    {
        return $this->hasMany(
            TrnUserDivisionPriority::class,
            'trn_division_id',
            'id'
        )->alive();
    }

    /**
     * レコードをGraphQLなどでの受け渡し用に変換.
     *
     * @param  array<mixed>  $with
     * @param  array<string>  $history
     * @return array<mixed>
     */
    public function toPayload(array $with = [], array $history = []): array
    {
        return [
            'id'                      => $this->id              ?? 0,
            'name'                    => $this->name            ?? '',
            'explain'                 => $this->explain         ?? '',
            'displayOrder'            => $this->display_order   ?? 0,

            'trnDivisionUser'         => $this->isExistKeyInWith($with, 'TrnDivisionUser', $history)
                ? $this->TrnDivisionUser->map(
                    fn (TrnDivisionUser $trnDivisionUser) => $trnDivisionUser->toPayload($with, [...$history, 'TrnDivisionUser'])
                )
                : [],

            'trnUserDivisionPriority' => $this->isExistKeyInWith($with, 'TrnUserDivisionPriority', $history)
                ? $this->TrnUserDivisionPriority->map(
                    fn (TrnUserDivisionPriority $trnUserDivisionPriority) => $trnUserDivisionPriority->toPayload($with, [...$history, 'TrnUserDivisionPriority'])
                )
                : [],
        ];
    }
}

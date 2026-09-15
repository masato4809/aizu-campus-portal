<?php

declare(strict_types=1);

namespace App\Models\App\Trn;

use App\Models\App\ModelBase;
use App\Trait\EagerLoadHelper;
use Database\Factories\App\Trn\TrnDivisionUserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin TrnDivisionUser
 *
 * @extends ModelBase<TrnDivisionUser>
 */
class TrnDivisionUser extends ModelBase
{
    /** @use HasFactory<TrnDivisionUserFactory> */
    use EagerLoadHelper, hasFactory;

    protected $table = 'trn_division_user';

    /**
     * リレーション:TrnUser
     *
     * @return HasOne<TrnUser, covariant TrnDivisionUser>
     */
    public function TrnUser(): HasOne
    {
        return $this->hasOne(
            TrnUser::class,
            'id',
            'trn_user_id'
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
            'id'            => $this->id              ?? 0,
            'trnDivisionId' => $this->trn_division_id ?? 0,
            'trnUserId'     => $this->trn_user_id     ?? 0,

            'trnUser'       => $this->isExistKeyInWith($with, 'TrnUser', $history)
                ? $this->TrnUser?->toPayload($with, [...$history, 'TrnUser'])
                : null,
        ];
    }
}

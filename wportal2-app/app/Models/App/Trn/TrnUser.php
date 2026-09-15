<?php

declare(strict_types=1);

namespace App\Models\App\Trn;

use App\Facades\Models\App\Trn\TrnUserService;
use App\Models\App\Auth\AuthUser;
use App\Models\App\ModelBase;
use App\Trait\EagerLoadHelper;
use Database\Factories\App\Trn\TrnUserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin TrnUser
 *
 * @extends ModelBase<TrnUser>
 */
class TrnUser extends ModelBase
{
    /** @use HasFactory<TrnUserFactory> */
    use EagerLoadHelper, HasFactory;

    protected $table = 'trn_user';

    /**
     * リレーション:AuthUser
     *
     * @return HasOne<AuthUser, covariant TrnUser>
     */
    public function AuthUser(): HasOne
    {
        return $this->hasOne(
            AuthUser::class,
            'id',
            'auth_id'
        );
    }

    /**
     * リレーション:TrnAttendanceState
     *
     * @return HasMany<TrnAttendanceState, covariant TrnUser>
     */
    public function TrnAttendanceState(): HasMany
    {
        return $this->hasMany(
            TrnAttendanceState::class,
            'trn_user_id',
            'id'
        )->alive();
    }

    /**
     *  リレーション:TrnUserSlackProfile
     *
     * @return HasOne<TrnUserSlackProfile, covariant TrnUser>
     */
    public function TrnUserSlackProfile(): HasOne
    {
        return $this->hasOne(
            TrnUserSlackProfile::class,
            'trn_user_id',
            'id'
        )->alive();
    }

    /**
     *  リレーション:TrnUserDivisionPriority
     *
     * @return HasMany<TrnUserDivisionPriority, covariant TrnUser>
     */
    public function TrnUserDivisionPriority(): HasMany
    {
        return $this->hasMany(
            TrnUserDivisionPriority::class,
            'trn_user_id',
            'id'
        )->alive();
    }

    /**
     *  リレーション:TrnUserProjectPriority
     *
     * @return HasMany<TrnUserProjectPriority, covariant TrnUser>
     */
    public function TrnUserProjectPriority(): HasMany
    {
        return $this->hasMany(
            TrnUserProjectPriority::class,
            'trn_user_id',
            'id'
        )->alive();
    }

    /**
     * リレーション:TrnUserReward
     *
     * @return HasMany<TrnUserReward, covariant TrnUser>
     */
    public function TrnUserReward(): HasMany
    {
        return $this->hasMany(
            TrnUserReward::class,
            'trn_user_id',
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
            'id'                  => $this->id                ?? 0,
            'authId'              => $this->auth_id           ?? 0,
            'nickname'            => $this->nickname          ?? '',
            'currentGold'         => $this->current_gold      ?? 0,
            'accumulationGold'    => $this->accumulation_gold ?? 0,
            'birthDate'           => $this->birth_date        ?? '',
            'selfIntroduction'    => $this->self_introduction ?? '',
            'faceImagePath'       => TrnUserService::getImageUrl($this),

            'authUser'            => $this->isExistKeyInWith($with, 'AuthUser', $history)
                ? $this->AuthUser?->toPayload($with, [...$history, 'AuthUser']) : null,

            'trnAttendanceState'  => $this->isExistKeyInWith($with, 'TrnAttendanceState', $history)
                ? $this->TrnAttendanceState->map(
                    fn (TrnAttendanceState $trnAttendanceState) => $trnAttendanceState->toPayload($with, [...$history, 'TrnAttendanceState'])
                )
                : [],

            'trnUserSlackProfile' => $this->isExistKeyInWith($with, 'TrnUserSlackProfile', $history)
                ? $this->TrnUserSlackProfile?->toPayload($with, [...$history, 'TrnUserSlackProfile'])
                : null,
        ];
    }
}

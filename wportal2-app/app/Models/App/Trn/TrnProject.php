<?php

declare(strict_types=1);

namespace App\Models\App\Trn;

use App\Models\App\ModelBase;
use App\Trait\EagerLoadHelper;
use Database\Factories\App\Trn\TrnProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin TrnProject
 *
 * @extends ModelBase<TrnProject>
 */
class TrnProject extends ModelBase
{
    /** @use HasFactory<TrnProjectFactory> */
    use EagerLoadHelper, hasFactory;

    protected $table = 'trn_project';

    /**
     * リレーション:TrnProjectUser
     *
     * @return HasMany<TrnProjectUser, covariant TrnProject>
     */
    public function TrnProjectUser(): HasMany
    {
        return $this->hasMany(
            TrnProjectUser::class,
            'trn_project_id',
            'id'
        )->alive();
    }

    /**
     * リレーション:TrnUserProjectPriority
     *
     * @return HasMany<TrnUserProjectPriority, covariant TrnProject>
     */
    public function TrnUserProjectPriority(): HasMany
    {
        return $this->hasMany(
            TrnUserProjectPriority::class,
            'trn_project_id',
            'id'
        )->alive();
    }

    /**
     * リレーション:TrnProjectNotification
     *
     * @return HasMany<TrnProjectNotification, covariant TrnProject>
     */
    public function TrnProjectNotification(): HasMany
    {
        return $this->hasMany(
            TrnProjectNotification::class,
            'trn_project_id',
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
            'id'                     => $this->id              ?? 0,
            'name'                   => $this->name            ?? '',
            'explain'                => $this->explain         ?? '',
            'displayOrder'           => $this->display_order   ?? 0,

            'trnProjectUser'         => $this->isExistKeyInWith($with, 'TrnProjectUser', $history)
                ? $this->TrnProjectUser->map(
                    fn (TrnProjectUser $trnProjectUser) => $trnProjectUser->toPayload($with, [...$history, 'TrnProjectUser'])
                )
                : [],

            'trnUserProjectPriority' => $this->isExistKeyInWith($with, 'TrnUserProjectPriority', $history)
                ? $this->TrnUserProjectPriority->map(
                    fn (TrnUserProjectPriority $trnUserProjectPriority) => $trnUserProjectPriority->toPayload($with, [...$history, 'TrnUserProjectPriority'])
                )
                : [],

            'trnProjectNotification' => $this->isExistKeyInWith($with, 'TrnProjectNotification', $history)
                ? $this->TrnProjectNotification->map(
                    fn (TrnProjectNotification $trnProjectNotification) => $trnProjectNotification->toPayload($with, [...$history, 'TrnProjectNotification'])
                )
                : [],
        ];
    }
}

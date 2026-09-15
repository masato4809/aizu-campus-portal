<?php

declare(strict_types=1);

namespace App\Models\App\Trn;

use App\Models\App\ModelBase;
use App\Trait\EagerLoadHelper;
use Database\Factories\App\Trn\TrnProjectUserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin TrnProjectUser
 *
 * @extends ModelBase<TrnProjectUser>
 */
class TrnProjectUser extends ModelBase
{
    /** @use HasFactory<TrnProjectUserFactory> */
    use EagerLoadHelper, hasFactory;

    protected $table = 'trn_project_user';

    /**
     * リレーション:TrnUser
     *
     * @return HasOne<TrnUser, covariant TrnProjectUser>
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
            'trnProjectId'  => $this->trn_project_id  ?? 0,
            'trnUserId'     => $this->trn_user_id     ?? 0,

            'trnUser'       => $this->isExistKeyInWith($with, 'TrnUser', $history)
                ? $this->TrnUser?->toPayload($with, [...$history, 'TrnUser'])
                : null,
        ];
    }
}

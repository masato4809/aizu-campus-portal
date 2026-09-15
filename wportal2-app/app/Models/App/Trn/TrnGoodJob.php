<?php

declare(strict_types=1);

namespace App\Models\App\Trn;

use App\Models\App\ModelBase;
use App\Trait\EagerLoadHelper;
use Database\Factories\App\Trn\TrnGoodJobFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin TrnGoodJob
 *
 * @extends ModelBase<TrnGoodJob>
 */
class TrnGoodJob extends ModelBase
{
    /** @use HasFactory<TrnGoodJobFactory> */
    use EagerLoadHelper, HasFactory;

    protected $table = 'trn_good_job';

    /**
     * リレーション:TrnUser(from)
     *
     * @return HasOne<TrnUser, covariant TrnGoodJob>
     */
    public function FromTrnUser(): HasOne
    {
        return $this->hasOne(
            TrnUser::class,
            'id',
            'from_trn_user_id'
        )->alive();
    }

    /**
     * リレーション:TrnDivision(from)
     *
     * @return HasOne<TrnDivision, covariant TrnGoodJob>
     */
    public function FromTrnDivision(): HasOne
    {
        return $this->hasOne(
            TrnDivision::class,
            'id',
            'from_trn_division_id'
        )->alive();
    }

    /**
     * リレーション:TrnProject(from)
     *
     * @return HasOne<TrnProject, covariant TrnGoodJob>
     */
    public function FromTrnProject(): HasOne
    {
        return $this->hasOne(
            TrnProject::class,
            'id',
            'from_trn_project_id'
        )->alive();
    }

    /**
     * リレーション:TrnUser(from)
     *
     * @return HasOne<TrnUser, covariant TrnGoodJob>
     */
    public function ToTrnUser(): HasOne
    {
        return $this->hasOne(
            TrnUser::class,
            'id',
            'to_trn_user_id'
        )->alive();
    }

    /**
     * リレーション:TrnDivision(to)
     *
     * @return HasOne<TrnDivision, covariant TrnGoodJob>
     */
    public function ToTrnDivision(): HasOne
    {
        return $this->hasOne(
            TrnDivision::class,
            'id',
            'to_trn_division_id'
        )->alive();
    }

    /**
     * リレーション:TrnProject(to)
     *
     * @return HasOne<TrnProject, covariant TrnGoodJob>
     */
    public function ToTrnProject(): HasOne
    {
        return $this->hasOne(
            TrnProject::class,
            'id',
            'to_trn_project_id'
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
            'id'                => $this->id                   ?? 0,
            'trnUserId'         => $this->trn_user_id          ?? 0,
            'fromTargetType'    => $this->from_target_type     ?? 0,
            'fromTrnUserId'     => $this->from_trn_user_id     ?? 0,
            'fromTrnDivisionId' => $this->from_trn_division_id ?? 0,
            'fromTrnProjectId'  => $this->from_trn_project_id  ?? 0,
            'fromOtherLabel'    => $this->from_other_label     ?? '',
            'toTargetType'      => $this->to_target_type       ?? 0,
            'toTrnUserId'       => $this->to_trn_user_id       ?? 0,
            'toTrnDivisionId'   => $this->to_trn_division_id   ?? 0,
            'toTrnProjectId'    => $this->to_trn_project_id    ?? 0,
            'toOtherLabel'      => $this->to_other_label       ?? '',
            'title'             => $this->title                ?? '',
            'content'           => $this->content              ?? '',
            'createdAt'         => $this->created_at           ?? '',

            // FromTrnUser.
            'fromTrnUser'       => $this->isExistKeyInWith($with, 'FromTrnUser', $history)
                ? $this->FromTrnUser?->toPayload($with, [...$history, 'FromTrnUser'])
                : null,

            // FromTrnDivision.
            'fromTrnDivision'   => $this->isExistKeyInWith($with, 'FromTrnDivision', $history)
                ? $this->FromTrnDivision?->toPayload($with, [...$history, 'FromTrnDivision'])
                : null,

            // FromTrnProject.
            'fromTrnProject'    => $this->isExistKeyInWith($with, 'FromTrnProject', $history)
                ? $this->FromTrnProject?->toPayload($with, [...$history, 'FromTrnProject'])
                : null,

            // ToTrnUser.
            'toTrnUser'         => $this->isExistKeyInWith($with, 'ToTrnUser', $history)
                ? $this->ToTrnUser?->toPayload($with, [...$history, 'ToTrnUser'])
                : null,

            // ToTrnDivision.
            'toTrnDivision'     => $this->isExistKeyInWith($with, 'ToTrnDivision', $history)
                ? $this->ToTrnDivision?->toPayload($with, [...$history, 'ToTrnDivision'])
                : null,

            // ToTrnProject.
            'toTrnProject'      => $this->isExistKeyInWith($with, 'ToTrnProject', $history)
                ? $this->ToTrnProject?->toPayload($with, [...$history, 'ToTrnProject'])
                : null,
        ];
    }
}

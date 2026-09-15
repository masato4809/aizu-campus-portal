<?php

declare(strict_types=1);

namespace App\Models\App\Trn;

use App\Models\App\ModelBase;
use Database\Factories\App\Trn\TrnUserDivisionPriorityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin TrnUserDivisionPriority
 *
 * @extends ModelBase<TrnUserDivisionPriority>
 */
class TrnUserDivisionPriority extends ModelBase
{
    /** @use HasFactory<TrnUserDivisionPriorityFactory> */
    use HasFactory;

    protected $table = 'trn_user_division_priority';

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
            'id'               => $this->id                ?? 0,
            'trnUserId'        => $this->trn_user_id       ?? 0,
            'trnDivisionId'    => $this->trn_division_id   ?? 0,
            'divisionPriority' => $this->division_priority ?? 0,
        ];
    }
}

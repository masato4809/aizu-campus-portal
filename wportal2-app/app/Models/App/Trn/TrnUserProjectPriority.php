<?php

declare(strict_types=1);

namespace App\Models\App\Trn;

use App\Models\App\ModelBase;
use Database\Factories\App\Trn\TrnUserProjectPriorityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin TrnUserProjectPriority
 *
 * @extends ModelBase<TrnUserProjectPriority>
 */
class TrnUserProjectPriority extends ModelBase
{
    /** @use HasFactory<TrnUserProjectPriorityFactory> */
    use HasFactory;

    protected $table = 'trn_user_project_priority';

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
            'id'              => $this->id               ?? 0,
            'trnUserId'       => $this->trn_user_id      ?? 0,
            'trnProjectId'    => $this->trn_project_id   ?? 0,
            'projectPriority' => $this->project_priority ?? 0,
        ];
    }
}

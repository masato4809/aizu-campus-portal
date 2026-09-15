<?php

declare(strict_types=1);

namespace App\Models\App\Trn;

use App\Enum\App\EArchiveLevel;
use App\Models\App\ModelBase;
use App\Trait\EagerLoadHelper;
use Database\Factories\App\Trn\TrnSeatFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin TrnSeat
 *
 * @extends ModelBase<TrnSeat>
 */
class TrnSeat extends ModelBase
{
    /** @use HasFactory<TrnSeatFactory> */
    use EagerLoadHelper, HasFactory;

    protected $table = 'trn_seat';

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
            'id'          => $this->id          ?? 0,
            'label'       => $this->label       ?? '',
            'positionX'   => $this->position_x  ?? 0,
            'positionY'   => $this->position_y  ?? 0,
            'phoneNumber' => $this->phone_number ?? '',
        ];
    }
}

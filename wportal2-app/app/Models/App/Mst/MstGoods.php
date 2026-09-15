<?php

declare(strict_types=1);

namespace App\Models\App\Mst;

use App\Models\App\ModelBase;
use App\Trait\EagerLoadHelper;

/**
 * @mixin MstGoods
 *
 * @extends ModelBase<MstGoods>
 */
class MstGoods extends ModelBase
{
    use EagerLoadHelper;

    protected $table = 'mst_goods';

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
            'id'          => $this->id,
            'name'        => $this->name            ?? '',
            'explain'     => $this->explain         ?? '',
            'comment'     => $this->comment         ?? '',
            'category'    => $this->category        ?? 0,
            'imagePath'   => $this->image_path      ?? '',
            'authorEmail' => $this->author_email    ?? '',
            'price'       => $this->price           ?? 0,
        ];
    }
}

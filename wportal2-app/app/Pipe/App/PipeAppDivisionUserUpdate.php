<?php

declare(strict_types=1);

namespace App\Pipe\App;

use App\Enum\App\EPipeKind;
use App\Pipe\PipeBase;
use Illuminate\Support\Collection;

final class PipeAppDivisionUserUpdate extends PipeBase
{
    public int $trnDivisionId = 0;

    /** @var Collection<int, int>|null */
    public ?Collection $trnUserIdList;

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::APP_DIVISION_USER_UPDATE, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        $this->trnDivisionId = $this->getArgAsInteger('trnDivisionId');
        $this->trnUserIdList = collect(
            $this->getArgAsIntArray('trnUserIdList')
        );
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void {}
}

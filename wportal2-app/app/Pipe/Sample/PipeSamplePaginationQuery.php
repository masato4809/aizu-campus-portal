<?php

declare(strict_types=1);

namespace App\Pipe\Sample;

use App\Enum\App\EPipeKind;
use App\Pipe\PipeBase;

final class PipeSamplePaginationQuery extends PipeBase
{
    public int $pageIndex = 0;

    public int $pageStep  = 0;

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::SAMPLE_PAGINATION_QUERY, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        $this->pageIndex = $this->getArgAsInteger('pageIndex');
        $this->pageStep  = $this->getArgAsInteger('pageStep');
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void {}
}

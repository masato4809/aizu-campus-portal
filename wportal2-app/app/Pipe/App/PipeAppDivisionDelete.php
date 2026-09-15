<?php

declare(strict_types=1);

namespace App\Pipe\App;

use App\Enum\App\EPipeKind;
use App\Pipe\PipeBase;

final class PipeAppDivisionDelete extends PipeBase
{
    public int $trnDivisionId = 0;

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::APP_DIVISION_DELETE, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        $this->trnDivisionId  = $this->getArgAsInteger('trnDivisionId');
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void {}
}

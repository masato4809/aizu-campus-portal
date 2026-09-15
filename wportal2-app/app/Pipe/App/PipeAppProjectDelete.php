<?php

declare(strict_types=1);

namespace App\Pipe\App;

use App\Enum\App\EPipeKind;
use App\Pipe\PipeBase;

final class PipeAppProjectDelete extends PipeBase
{
    public int $trnProjectId = 0;

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::APP_PROJECT_DELETE, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        $this->trnProjectId  = $this->getArgAsInteger('trnProjectId');
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void {}
}

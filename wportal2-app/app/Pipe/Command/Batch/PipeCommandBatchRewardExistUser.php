<?php

declare(strict_types=1);

namespace App\Pipe\Command\Batch;

use App\Enum\App\EPipeKind;
use App\Pipe\PipeBase;

final class PipeCommandBatchRewardExistUser extends PipeBase
{
    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::COMMAND_BATCH_REWARD_EXIST_USER, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void {}

    /**
     * バリデーション実行.
     */
    public function validate(): void {}
}

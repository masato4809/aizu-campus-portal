<?php

declare(strict_types=1);

namespace App\Pipe\Sample;

use App\Enum\App\EPipeKind;
use App\Pipe\PipeBase;

final class PipeSampleRedisQuery extends PipeBase
{
    public string $sampleArgument = '';

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::SAMPLE_REDIS_QUERY, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        $this->sampleArgument = $this->getArgAsString('sampleArgument');
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void {}
}

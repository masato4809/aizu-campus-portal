<?php

declare(strict_types=1);

namespace App\Pipe\Sample;

use App\Enum\App\EPipeKind;
use App\Enum\App\EValidationType;
use App\Enum\ValidationRules\ValidationPacker;
use App\Pipe\PipeBase;

final class PipeSampleRedisUpdate extends PipeBase
{
    public string $storeValue = '';

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::SAMPLE_REDIS_UPDATE, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        $this->storeValue = $this->getArgAsString('storeValue');
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void
    {
        $packer    = new ValidationPacker;

        $packer->addValidation(
            'storeValue',
            'Redis保存値',
            $this->storeValue,
            collect([
                EValidationType::REQUIRED->ruleSet(),
            ])
        );

        // バリデーションの実施.
        $validator = $packer->makeValidator();
        if ($validator->fails()) {
            $this->errors = $validator->errors();
        }
    }
}

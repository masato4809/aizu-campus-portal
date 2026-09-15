<?php

declare(strict_types=1);

namespace App\Pipe\Sample;

use App\Enum\App\EPipeKind;
use App\Enum\App\EValidationType;
use App\Enum\ValidationRules\ValidationPacker;
use App\Pipe\PipeBase;

final class PipeSampleInputFormUpdate extends PipeBase
{
    public string $inputSingleNumber  = '';

    public string $inputSingleNotZero = '';

    public string $inputSingleRange   = '';

    public string $inputSingleEmail   = '';

    public string $inputMulti         = '';

    public int $inputSelectStringId   = 0;

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::SAMPLE_INPUT_FORM_UPDATE, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        $this->inputSingleNumber   = $this->getArgAsString('inputSingleNumber');
        $this->inputSingleNotZero  = $this->getArgAsString('inputSingleNotZero');
        $this->inputSingleRange    = $this->getArgAsString('inputSingleRange');
        $this->inputSingleEmail    = $this->getArgAsString('inputSingleEmail');
        $this->inputMulti          = $this->getArgAsString('inputMulti');
        $this->inputSelectStringId = $this->getArgAsInteger('inputSelectStringId');
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void
    {
        $packer    = new ValidationPacker;

        // Input(single).
        $packer->addValidation(
            'inputSingleNumber',
            'input(single:整数)',
            $this->inputSingleNumber,
            collect([
                EValidationType::REQUIRED->ruleSet(),
                EValidationType::NUMBER->ruleSet(),
            ])
        );
        $packer->addValidation(
            'inputSingleNotZero',
            'input(single:非0)',
            $this->inputSingleNotZero,
            collect([
                EValidationType::REQUIRED_NOT_ZERO->ruleSet(),
            ])
        );
        $packer->addValidation(
            'inputSingleRange',
            'input(single:範囲)',
            $this->inputSingleRange,
            collect([
                EValidationType::NUMBER_RANGE->ruleSet(20, 50),
            ])
        );
        $packer->addValidation(
            'inputSingleEmail',
            'input(email)',
            $this->inputSingleEmail,
            collect([
                EValidationType::EMAIL->ruleSet(),
            ])
        );

        // Input(multi)
        $packer->addValidation(
            'inputMulti',
            'input(multi)',
            $this->inputMulti,
            collect([
                EValidationType::REQUIRED->ruleSet(),
                EValidationType::LENGTH->ruleSet(100),
            ]),
        );

        // Input(select)
        $packer->addValidation(
            'inputSelectStringId',
            'input(select)',
            $this->inputSelectStringId,
            collect([
                EValidationType::REQUIRED_NOT_ZERO->ruleSet(),
            ]),
        );

        // バリデーションの実施.
        $validator = $packer->makeValidator();
        if ($validator->fails()) {
            $this->errors = $validator->errors();
        }
    }
}

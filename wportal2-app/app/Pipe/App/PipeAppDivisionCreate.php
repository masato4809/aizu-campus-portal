<?php

declare(strict_types=1);

namespace App\Pipe\App;

use App\Enum\App\EPipeKind;
use App\Enum\App\EValidationType;
use App\Enum\ValidationRules\ValidationPacker;
use App\Models\App\Auth\AuthUser;
use App\Pipe\PipeBase;

final class PipeAppDivisionCreate extends PipeBase
{
    public string $name                 = '';

    public string $explain              = '';

    public ?AuthUser $createdAuthUser   = null;

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::APP_DIVISION_CREATE, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        $this->name     = $this->getArgAsString('name');
        $this->explain  = $this->getArgAsString('explain');
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void
    {
        $packer    = new ValidationPacker;

        // 名前.
        $packer->addValidation(
            'name',
            '名前',
            $this->name,
            collect([
                EValidationType::REQUIRED->ruleSet(),
            ])
        );

        // 表示名.
        $packer->addValidation(
            'explain',
            '説明',
            $this->explain,
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

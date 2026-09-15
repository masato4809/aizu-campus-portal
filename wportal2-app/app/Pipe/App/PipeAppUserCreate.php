<?php

declare(strict_types=1);

namespace App\Pipe\App;

use App\Enum\App\EPipeKind;
use App\Enum\App\EValidationType;
use App\Enum\ValidationRules\ValidationPacker;
use App\Models\App\Auth\AuthUser;
use App\Pipe\PipeBase;

final class PipeAppUserCreate extends PipeBase
{
    public string $name               = '';

    public string $nickname           = '';

    public string $email              = '';

    public ?AuthUser $createdAuthUser = null;

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::APP_USER_CREATE, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        $this->name     = $this->getArgAsString('name');
        $this->nickname = $this->getArgAsString('nickname');
        $this->email    = $this->getArgAsString('email');
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
            'nickname',
            '表示名',
            $this->nickname,
            collect([
                EValidationType::REQUIRED->ruleSet(),
            ])
        );

        // E-Mail.
        $packer->addValidation(
            'email',
            'E-Mail',
            $this->email,
            collect([
                EValidationType::REQUIRED->ruleSet(),
                EValidationType::EMAIL->ruleSet(),
                EValidationType::UNIQUE_AUTH_EMAIL->ruleSet(),
            ])
        );

        // バリデーションの実施.
        $validator = $packer->makeValidator();
        if ($validator->fails()) {
            $this->errors = $validator->errors();
        }
    }
}

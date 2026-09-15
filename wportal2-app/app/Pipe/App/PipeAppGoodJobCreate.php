<?php

declare(strict_types=1);

namespace App\Pipe\App;

use App\Enum\App\EPipeKind;
use App\Enum\App\EValidationType;
use App\Enum\App\GoodJob\ETargetType;
use App\Enum\ValidationRules\ValidationPacker;
use App\Models\App\Auth\AuthUser;
use App\Pipe\PipeBase;
use Illuminate\Support\Facades\Auth;

final class PipeAppGoodJobCreate extends PipeBase
{
    public ETargetType $fromTargetType = ETargetType::INVALID;

    public int $fromTrnUserId          = 0;

    public int $fromTrnDivisionId      = 0;

    public int $fromTrnProjectId       = 0;

    public string $fromOtherLabel      = '';

    public ETargetType $toTargetType   = ETargetType::INVALID;

    public int $toTrnUserId            = 0;

    public int $toTrnDivisionId        = 0;

    public int $toTrnProjectId         = 0;

    public string $toOtherLabel        = '';

    public string $title               = '';

    public string $content             = '';

    public ?AuthUser $authUser         = null;

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::APP_GOOD_JOB_CREATE, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        // 送信元情報.
        $this->fromTargetType    = ETargetType::tryFrom(
            $this->getArgAsInteger('fromTargetType')
        ) ?? ETargetType::INVALID;
        $this->fromTrnUserId     = $this->getArgAsInteger('fromTrnUserId');
        $this->fromTrnDivisionId = $this->getArgAsInteger('fromTrnDivisionId');
        $this->fromTrnProjectId  = $this->getArgAsInteger('fromTrnProjectId');
        $this->fromOtherLabel    = $this->getArgAsString('fromOtherLabel');

        // 送信先情報.
        $this->toTargetType      = ETargetType::tryFrom(
            $this->getArgAsInteger('toTargetType')
        ) ?? ETargetType::INVALID;
        $this->toTrnUserId       = $this->getArgAsInteger('toTrnUserId');
        $this->toTrnDivisionId   = $this->getArgAsInteger('toTrnDivisionId');
        $this->toTrnProjectId    = $this->getArgAsInteger('toTrnProjectId');
        $this->toOtherLabel      = $this->getArgAsString('toOtherLabel');

        // 内容.
        $this->title             = $this->getArgAsString('title');
        $this->content           = $this->getArgAsString('content');

        $this->authUser          = Auth::user();
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void
    {
        $packer    = new ValidationPacker;

        // 送信元バリデーション.
        switch ($this->fromTargetType) {
            case ETargetType::USER:
                $packer->addValidation(
                    'fromTrnUserId',
                    'ユーザー',
                    $this->fromTrnUserId,
                    collect([
                        EValidationType::REQUIRED_NOT_ZERO->ruleSet(),
                    ])
                );
                break;
            case ETargetType::DIVISION:
                $packer->addValidation(
                    'fromTrnDivisionId',
                    '部署',
                    $this->fromTrnDivisionId,
                    collect([
                        EValidationType::REQUIRED_NOT_ZERO->ruleSet(),
                    ])
                );
                break;
            case ETargetType::PROJECT:
                $packer->addValidation(
                    'fromTrnProjectId',
                    'プロジェクト',
                    $this->fromTrnProjectId,
                    collect([
                        EValidationType::REQUIRED_NOT_ZERO->ruleSet(),
                    ])
                );
                break;
            case ETargetType::LABEL:
                $packer->addValidation(
                    'fromOtherLabel',
                    'ラベル',
                    $this->fromOtherLabel,
                    collect([
                        EValidationType::REQUIRED->ruleSet(),
                    ])
                );
                break;
            default:
                break;
        }

        // 送信先バリデーション.
        switch ($this->toTargetType) {
            case ETargetType::USER:
                $packer->addValidation(
                    'toTrnUserId',
                    'ユーザー',
                    $this->toTrnUserId,
                    collect([
                        EValidationType::REQUIRED_NOT_ZERO->ruleSet(),
                    ])
                );
                break;
            case ETargetType::DIVISION:
                $packer->addValidation(
                    'toTrnDivisionId',
                    '部署',
                    $this->toTrnDivisionId,
                    collect([
                        EValidationType::REQUIRED_NOT_ZERO->ruleSet(),
                    ])
                );
                break;
            case ETargetType::PROJECT:
                $packer->addValidation(
                    'toTrnProjectId',
                    'プロジェクト',
                    $this->toTrnProjectId,
                    collect([
                        EValidationType::REQUIRED_NOT_ZERO->ruleSet(),
                    ])
                );
                break;
            case ETargetType::LABEL:
                $packer->addValidation(
                    'toOtherLabel',
                    'ラベル',
                    $this->toOtherLabel,
                    collect([
                        EValidationType::REQUIRED->ruleSet(),
                    ])
                );
                break;
            default:
                break;
        }

        // タイトル.
        $packer->addValidation(
            'title',
            'タイトル',
            $this->title,
            collect([
                EValidationType::REQUIRED->ruleSet(),
            ])
        );

        // 内容.
        $packer->addValidation(
            'content',
            '内容',
            $this->content,
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

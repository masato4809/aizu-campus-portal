<?php

declare(strict_types=1);

namespace App\Pipe\App;

use App\Enum\App\EPipeKind;
use App\Enum\App\EValidationType;
use App\Enum\ValidationRules\ValidationPacker;
use App\Pipe\PipeBase;
use Illuminate\Http\UploadedFile;

final class PipeAppPersonalSettingEditUpdate extends PipeBase
{
    public int $trnUserId           = 0;

    public string $nickname         = '';

    public ?string $birthDate       = null;

    public string $selfIntroduction = '';

    public string $slackUserId      = '';

    public string $slackUserName    = '';

    public string $slackTeamId      = '';

    public ?UploadedFile $upload    = null;

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::APP_PERSONAL_SETTING_EDIT_UPDATE, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        $this->trnUserId         = $this->getArgAsInteger('trnUserId');
        $this->nickname          = $this->getArgAsString('nickname');
        $birthDate               = $this->getArgAsString('birthDate');
        $this->birthDate         = empty($birthDate) ? null : $birthDate;
        $this->selfIntroduction  = $this->getArgAsString('selfIntroduction');
        $this->slackUserId       = $this->getArgAsString('slackUserId');
        $this->slackUserName     = $this->getArgAsString('slackUserName');
        $this->slackTeamId       = $this->getArgAsString('slackTeamId');

        // image file upload.
        if (isset($this->args['upload'])) {
            /** @var UploadedFile $upload */
            $upload              = $this->args['upload'];
            $this->upload        = $upload;
        }
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void
    {
        $packer    = new ValidationPacker;

        // 表示名.
        $packer->addValidation(
            'nickname',
            '表示名',
            $this->nickname,
            collect([
                EValidationType::REQUIRED->ruleSet(),
            ])
        );

        // 日付.
        $packer->addValidation(
            'birthDate',
            '生年月日',
            $this->birthDate,
            collect([
                EValidationType::DATE->ruleSet(),
            ])
        );

        // バリデーションの実施.
        $validator = $packer->makeValidator();
        if ($validator->fails()) {
            $this->errors = $validator->errors();
        }
    }
}

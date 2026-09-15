<?php

declare(strict_types=1);

namespace App\Services\Models\App\Trn;

use App\Enum\App\EPipeKind;
use App\Facades\Internal\PipeService;
use App\Models\App\Trn\TrnUserSlackProfile;
use App\Pipe\App\PipeAppPersonalSettingEditUpdate;
use App\Pipe\App\PipeAppPersonalSettingShowAlignSlack;
use App\Pipe\PipeBase;
use App\Services\Models\ModelServiceBase;
use Exception;
use Throwable;

class TrnUserSlackProfileService extends ModelServiceBase
{
    /**
     * パイプによる更新.
     *
     * @throws Throwable
     */
    public function updateByPipe(): void
    {
        foreach (PipeService::getPipes(__CLASS__) as $pipe) {
            $kind = $pipe->getKind();
            match ($kind) {
                EPipeKind::APP_PERSONAL_SETTING_SHOW_ALIGN_SLACK => $this->execAppPersonalSettingShowAlignSlack($pipe),
                EPipeKind::APP_PERSONAL_SETTING_EDIT_UPDATE      => $this->execAppPersonalSettingEditUpdate($pipe),
                default                                          => throw new Exception,
            };
            $pipe->onUpdate(__CLASS__);
        }
    }

    /**
     * @throws Exception
     */
    private function execAppPersonalSettingShowAlignSlack(
        PipeBase $pipe
    ): void {
        /** @var PipeAppPersonalSettingShowAlignSlack $pipe */
        // 更新もしくは生成.
        $trnUserSlackProfile = $this->findByTrnUserId($pipe->trnUserId);
        if (is_null($trnUserSlackProfile)) {
            $trnUserSlackProfile                  = new TrnUserSlackProfile;
            $trnUserSlackProfile->trn_user_id     = $pipe->trnUserId;
            $trnUserSlackProfile->slack_user_id   = $pipe->slackUserId;
            $trnUserSlackProfile->slack_user_name = $pipe->slackUserName;
            $trnUserSlackProfile->slack_team_id   = $pipe->slackTeamId;
            $this->insertOrFail($trnUserSlackProfile);
        } else {
            $trnUserSlackProfile->slack_user_id   = $pipe->slackUserId;
            $trnUserSlackProfile->slack_user_name = $pipe->slackUserName;
            $trnUserSlackProfile->slack_team_id   = $pipe->slackTeamId;
            $this->updateOrFail($trnUserSlackProfile);
        }
    }

    /**
     * @throws Throwable
     */
    private function execAppPersonalSettingEditUpdate(
        PipeBase $pipe
    ): void {
        /** @var PipeAppPersonalSettingEditUpdate $pipe */
        // 更新もしくは生成.
        $trnUserSlackProfile = $this->findByTrnUserId($pipe->trnUserId);
        if (is_null($trnUserSlackProfile)) {
            $trnUserSlackProfile                  = new TrnUserSlackProfile;
            $trnUserSlackProfile->trn_user_id     = $pipe->trnUserId;
            $trnUserSlackProfile->slack_user_id   = $pipe->slackUserId;
            $trnUserSlackProfile->slack_user_name = $pipe->slackUserName;
            $trnUserSlackProfile->slack_team_id   = $pipe->slackTeamId;
            $this->insertOrFail($trnUserSlackProfile);
        } else {
            $trnUserSlackProfile->slack_user_id   = $pipe->slackUserId;
            $trnUserSlackProfile->slack_user_name = $pipe->slackUserName;
            $trnUserSlackProfile->slack_team_id   = $pipe->slackTeamId;
            $this->updateOrFail($trnUserSlackProfile);
        }
    }

    /**
     * ユーザーIDによる検索.
     */
    public function findByTrnUserId(int $trnUserId): ?TrnUserSlackProfile
    {
        /** @var TrnUserSlackProfile|null */
        return TrnUserSlackProfile::query()
            ->where('trn_user_id', $trnUserId)
            ->alive()
            ->first();
    }
}

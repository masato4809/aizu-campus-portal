<?php

declare(strict_types=1);

namespace App\Services\External;

use App\Enum\App\ENotificationType;
use App\Enum\App\EPipeKind;
use App\Enum\App\EStatusCode;
use App\Facades\Internal\PipeService;
use App\Facades\Models\App\Trn\TrnProjectNotificationService;
use App\Facades\Models\App\Trn\TrnProjectUserService;
use App\Models\App\Trn\TrnProjectNotification;
use App\Pipe\App\PipeAppAttendanceCreate;
use App\Pipe\App\PipeAppPersonalSettingShowAlignSlack;
use App\Pipe\App\PipeAppShuffleLunchRegister;
use App\Pipe\PipeBase;
use App\Trait\SlackApi;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Slackに対する処理.
 */
class SlackService
{
    use SlackApi;

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
                EPipeKind::APP_ATTENDANCE_CREATE                 => $this->execAppAttendanceCreate($pipe),
                EPipeKind::APP_PERSONAL_SETTING_SHOW_ALIGN_SLACK => $this->execAppPersonalSettingShowAlignSlack($pipe),
                EPipeKind::APP_SHUFFLE_LUNCH_REGISTER            => $this->execAppShuffleLunchRegister($pipe),
                default                                          => throw new Exception("invalid pipe:$kind->name")
            };
        }
    }

    /**
     * @throws Exception
     */
    private function execAppAttendanceCreate(
        PipeBase $pipe
    ): void {
        /** @var PipeAppAttendanceCreate $pipe */
        // 表示メッセージの作成.
        $localeDate          = Carbon::now()->format('Y-m-d H:i:s');
        $nickName            = $pipe->authUser?->TrnUser?->nickname ?: '';
        $stateLabel          = $pipe->eAttendanceState->getStateLabel();
        $message             = "${localeDate} ${nickName}さんが${stateLabel}";

        // ユーザーの所属するPJのID配列を特定.
        /** @var array<int> $targetProjectIdList */
        $targetProjectIdList = TrnProjectUserService::listByTrnUserId($pipe->authUser?->TrnUser?->id ?: 0)
            ->pluck('trn_project_id')
            ->toArray();

        // 各PJのSlack通知チャンネルを取得
        TrnProjectNotificationService::listByTrnProjectId($targetProjectIdList)
            // 通知タイプがSlackのものを抽出.
            ->where('notification_type', ENotificationType::SLACK_CHANNEL->value)
            ->each(function (Model $trnProjectNotification) use ($message) {
                /** @var TrnProjectNotification $trnProjectNotification */
                $this->slackApiPostMessage($trnProjectNotification->notification_value, $message);
            });
    }

    private function execAppPersonalSettingShowAlignSlack(
        PipeBase $pipe
    ): void {
        /** @var PipeAppPersonalSettingShowAlignSlack $pipe */

        // APIを実行.
        $result      = $this->slackApiLookupByEmail($pipe->email);

        // 結果を取得.
        /** @var array{ok: bool|null, user: array{id: string, name: string, team_id: string}} $resultArray */
        $resultArray = json_decode((string) $result, true);
        if (isset($resultArray['ok']) && $resultArray['ok'] === true) {
            $pipe->slackUserId   = $resultArray['user']['id'];
            $pipe->slackUserName = $resultArray['user']['name'];
            $pipe->slackTeamId   = $resultArray['user']['team_id'];

            return;
        }

        // PipeServiceにエラー結果を登録してthrow.
        Log::error(print_r($resultArray, true));
        PipeService::throwResult(
            EStatusCode::NOT_FOUND,
            new Exception("APIによる検索不可:$pipe->email")
        );
    }

    /**
     * @throws Exception
     */
    private function execAppShuffleLunchRegister(
        PipeBase $pipe
    ): void {
        /** @var PipeAppShuffleLunchRegister $pipe */
        $entryCount = $pipe->entryCount;

        // 表示メッセージの作成
        $message    = "{$entryCount}名が参加中！　シャッフルランチへのご参加をお待ちしております！";

        $this->notifyNotice($message, 'シャッフルランチ登録通知');
    }

    /**
     * 情報チャンネルへの通知を行う.
     *
     * @param  array<mixed>  $appendParams
     */
    public function notifyNotice(?string $message, ?string $title = null, ?string $file = null, array $appendParams = []): void
    {
        $url         = (string) config('slack.NoticeUrl');
        $env         = (string) config('app.env');
        $fixedTitle  = "[env]:`$env`";
        $fixedFile   = '';

        // タイトル指定があれば追加.
        if (isset($title)) {
            $fixedTitle .= ' '.$title;
        }

        // ファイル指定があれば追加.
        if (isset($file)) {
            $fixedFile .= $file;
        }

        $postMessage = <<<EOF
*$fixedTitle*
[type]: `Notice`
[file]: $fixedFile
EOF;

        // メッセージ指定があればコードブロックで投入する.
        if ($message !== null) {
            $postMessage .= "\n```\n$message\n```";
        }

        $this->notify($postMessage, $url, $appendParams);
    }

    /**
     * エラーチャンネルへの通知を行う.
     *
     * @param  array<mixed>  $appendParams
     */
    public function notifyError(?string $message, ?string $title = null, ?string $file = null, array $appendParams = []): void
    {
        $url         = (string) config('slack.ErrorUrl');
        $env         = (string) config('app.env');
        $fixedTitle  = "[env]:`$env`";
        $fixedFile   = '';

        // タイトル指定があれば追加.
        if (isset($title)) {
            $fixedTitle .= ' '.$title;
        }

        // ファイル指定があれば追加.
        if (isset($file)) {
            $fixedFile .= $file;
        }

        $postMessage = <<<EOF
*$fixedTitle*
[type]: `Notice`
[file]: $fixedFile
EOF;

        // メッセージ指定があればコードブロックで投入する.
        if ($message !== null) {
            $postMessage .= "\n```\n$message\n```";
        }

        // prd環境のみ[通知タグ]を設定.
        if (isPrd()) {
            $postMessage = "<!here>\n".$postMessage;
        }

        $this->notify($postMessage, $url, $appendParams);
    }

    /**
     * メッセージ・HooksUrlを指定して通知を行う.
     *
     * @param  array<mixed>  $appendParams
     */
    public function notify(string $message, string $url, array $appendParams): void
    {
        if (empty($url)) {
            return;
        }

        $params  = [
            // メッセージ本文.
            'text' => $message,
        ];
        $params  = array_merge($params, $appendParams);

        // 送信オプション.
        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_HTTPHEADER     => ['Content-type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($params),
        ];

        // curlで送信
        $curl    = curl_init();
        curl_setopt_array($curl, $options);
        curl_exec($curl);
        curl_close($curl);
    }
}

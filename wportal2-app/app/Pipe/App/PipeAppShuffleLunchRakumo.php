<?php

declare(strict_types=1);

namespace App\Pipe\App;

use App\Enum\App\EEnableFlag;
use App\Enum\App\EPipeKind;
use App\Enum\App\ShuffleLunch\EEventTimeZone;
use App\Facades\Models\App\Trn\TrnShuffleLunchGroupService;
use App\Models\App\Auth\AuthUser;
use App\Models\App\Trn\TrnShuffleLunchGroup;
use App\Pipe\PipeBase;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

final class PipeAppShuffleLunchRakumo extends PipeBase
{
    public ?AuthUser $authUser           = null;

    /** @var Collection<int, int> */
    public Collection $idList;

    /** @var Collection<int, string> */
    public Collection $emailList;

    public Carbon $start;

    public Carbon $end;

    public string $summary;

    public bool $isBlocked;

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::APP_SHUFFLE_LUNCH_RAKUMO, $args);
    }

    /**
     * データ取得.
     *
     * @throws Exception
     */
    public function parse(): void
    {
        // 対象となるidのリスト.
        $this->idList         = new Collection($this->getArgAsIntArray('idList'));

        // 対象のグループ情報・メルアド情報を取得.
        /** @var Collection<int, TrnShuffleLunchGroup> $groupList */
        $groupList            = TrnShuffleLunchGroupService::listByIdList(
            $this->idList,
            [
                'TrnUser',
                'TrnUser.AuthUser',
            ]
        );

        // 認証ユーザー.
        $this->authUser       = Auth::user();

        // メルアド情報を取得.
        // @phpstan-ignore argument.templateType
        $this->emailList      = $groupList->map(function (TrnShuffleLunchGroup $trnShuffleLunchGroup) {
            return $trnShuffleLunchGroup->TrnUser?->AuthUser?->email ?: '';
        })->filter(function (string $email) {
            return str_contains($email, '@919.jp');
        })->reject(function (string $email) {
            return $email === $this->authUser?->email ?: '';
        });

        // うち一件を取得.
        $trnShuffleLunchGroup = $groupList->first();
        if (! is_null($trnShuffleLunchGroup)) {
            // タイムゾーンからblock時間を取得.
            $eTimeZone       = EEventTimeZone::tryFrom(
                $trnShuffleLunchGroup->event_time_zone
            ) ?? EEventTimeZone::INVALID;
            $this->start     = $eTimeZone->getStart();
            $this->end       = $eTimeZone->getEnd();

            // ブロック名.
            $this->summary   = "シャッフルランチ[$trnShuffleLunchGroup->event_time_zone-$trnShuffleLunchGroup->group_id]";

            // ブロック済みかどうか.
            $this->isBlocked = $trnShuffleLunchGroup->rakumo_blocked === EEnableFlag::ENABLE->value;
        }
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void {}
}

<?php

declare(strict_types=1);

namespace App\Pipe\App;

use App\Enum\App\EPipeKind;
use App\Facades\Models\App\Mst\MstGoodsService;
use App\Models\App\Auth\AuthUser;
use App\Models\App\Mst\MstGoods;
use App\Pipe\PipeBase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

final class PipeAppShopLunchTicket extends PipeBase
{
    public int $mstGoodsId     = 0;

    public string $target      = '';

    public ?AuthUser $authUser = null;

    public ?MstGoods $mstGoods = null;

    public ?Carbon $start      = null;

    public ?Carbon $end        = null;

    /**
     * constructor.
     *
     * @param  array<mixed>  $args
     */
    public function __construct(array $args)
    {
        parent::__construct(EPipeKind::APP_SHOP_LUNCH_TICKET, $args);
    }

    /**
     * データ取得.
     */
    public function parse(): void
    {
        $this->mstGoodsId = $this->getArgAsInteger('mstGoodsId');
        $this->target     = $this->getArgAsString('target');
    }

    /**
     * バリデーション実行.
     */
    public function validate(): void {}

    /**
     * データ準備
     */
    public function prepare(): void
    {
        // 認証ユーザー取得.
        $this->authUser            = Auth::user();

        // データ取得.
        $this->mstGoods            = MstGoodsService::findOrFail($this->mstGoodsId);

        // 開始時間.
        $this->start               = Carbon::createFromTimeString($this->target);

        // 終了時間.
        $this->end                 = Carbon::createFromTimeString($this->target)->addHour();
    }
}

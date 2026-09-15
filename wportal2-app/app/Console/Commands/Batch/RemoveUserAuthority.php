<?php

declare(strict_types=1);

namespace App\Console\Commands\Batch;

use App\Enum\App\EUserAuthority;
use App\Facades\Models\App\Trn\TrnUserAuthorityService;
use App\Facades\Models\App\Trn\TrnUserService;
use Illuminate\Console\Command;

class RemoveUserAuthority extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature   = 'batch:RemoveUserAuthority {userId} {eAuthority}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '指定IDのユーザーの指定権利を削除する';

    /**
     * New a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // パラメータ取得.
        $userId                         = (int) $this->argument('userId');
        $eAuthority                     = EUserAuthority::tryFrom(
            (int) $this->argument('eAuthority')
        ) ?? EUserAuthority::INVALID;

        // パラメータが無効.
        if ($eAuthority === EUserAuthority::INVALID) {
            $this->error("権限が正しくありません:$eAuthority->value");

            return self::FAILURE;
        }

        // 対象ユーザーの取得.
        TrnUserService::findByIdOrFail($userId);

        // 既存権限の検索.
        $authority                      = TrnUserAuthorityService::findByUserIdAndAuthority(
            $userId,
            $eAuthority
        );
        if (is_null($authority)) {
            $this->info("権限を所持していません:$eAuthority->value");

            return self::SUCCESS;
        }
        TrnUserAuthorityService::deleteOrFail($authority);

        return self::SUCCESS;
    }
}

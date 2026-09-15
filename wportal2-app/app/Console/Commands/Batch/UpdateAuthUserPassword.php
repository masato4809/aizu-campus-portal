<?php

declare(strict_types=1);

namespace App\Console\Commands\Batch;

use App\Facades\Models\App\Auth\AuthUserService;
use Illuminate\Console\Command;

class UpdateAuthUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature   = 'batch:UpdateAuthUserPassword {authUserId} {value}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '指定IDの認証ユーザーのパスワードを更新する';

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
        // 引数より対象のemailを取得.
        $authUserId         = $this->argument('authUserId');
        $password           = $this->argument('value');
        if ($authUserId == null || $password == null) {
            return self::FAILURE;
        }

        $authUser           = AuthUserService::findByIdOrFail((int) $authUserId);

        // 値の更新.
        $authUser->password = $password;
        AuthUserService::updateOrFail($authUser);

        return self::SUCCESS;
    }
}

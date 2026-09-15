<?php

declare(strict_types=1);

namespace App\Console\Commands\Tool;

use App\Enum\App\EArchiveLevel;
use App\Enum\App\EUserAuthority;
use App\Models\App\Auth\AuthUser;
use App\Models\App\Trn\TrnUser;
use App\Models\App\Trn\TrnUserAuthority;
use Illuminate\Console\Command;

/**
 * 既存ユーザーに権限を付与するコマンド.
 *
 * 使用例:
 *   php artisan tool:GrantUserAuthority --email=user@example.com --authority=ADMIN_PRIVILEGE
 *   php artisan tool:GrantUserAuthority  (対話モード)
 */
class GrantUserAuthority extends Command
{
    protected $signature   = 'tool:GrantUserAuthority
        {--email= : 対象ユーザーのメールアドレス}
        {--authority= : 付与する権限 (ADMIN_PRIVILEGE, ADMIN_COMMAND, ROOT_PRIVILEGE)}';

    protected $description = '既存ユーザーに権限を付与する';

    public function handle(): int
    {
        $emailOption = $this->option('email');
        $email       = is_string($emailOption) ? $emailOption : $this->askForEmail();
        if ($email === null) {
            return self::FAILURE;
        }

        /** @var AuthUser|null $authUser */
        $authUser    = AuthUser::query()->where('email', $email)->first();
        if ($authUser === null) {
            $this->error("ユーザーが見つかりません: {$email}");

            return self::FAILURE;
        }

        /** @var TrnUser|null $trnUser */
        $trnUser     = TrnUser::query()->where('auth_id', $authUser->id)->first();
        if ($trnUser === null) {
            $this->error("TrnUser が見つかりません (auth_id: {$authUser->id})");

            return self::FAILURE;
        }

        $authority   = $this->resolveAuthority();
        if ($authority === null) {
            return self::FAILURE;
        }

        // 既に同じ権限を持っているか確認
        $exists      = TrnUserAuthority::query()
            ->where('trn_user_id', $trnUser->id)
            ->where('e_user_authority', $authority->value)
            ->where('e_archive_level', EArchiveLevel::ALIVE->value)
            ->exists();

        if ($exists) {
            $this->warn("ユーザー {$email} は既に {$authority->name} 権限を持っています。");

            return self::SUCCESS;
        }

        $record      = new TrnUserAuthority;
        $record->forceFill([
            'trn_user_id'      => $trnUser->id,
            'e_user_authority' => $authority->value,
            'e_archive_level'  => EArchiveLevel::ALIVE->value,
        ]);
        $record->save();

        $this->info("権限を付与しました: {$email} → {$authority->name}");

        return self::SUCCESS;
    }

    private function askForEmail(): ?string
    {
        $keyword  = $this->ask('メールアドレス（部分一致で検索）を入力してください');
        if ($keyword === null || $keyword === '') {
            $this->error('メールアドレスが入力されませんでした。');

            return null;
        }

        /** @var \Illuminate\Database\Eloquent\Collection<int, AuthUser> $users */
        $users    = AuthUser::query()
            ->where('email', 'LIKE', "%{$keyword}%")
            ->limit(20)
            ->get(['id', 'name', 'email']);

        if ($users->isEmpty()) {
            $this->error("該当するユーザーが見つかりません: {$keyword}");

            return null;
        }

        if ($users->count() === 1) {
            /** @var AuthUser $user */
            $user = $users->first();
            $this->info("対象: {$user->name} <{$user->email}>");

            return $user->email;
        }

        $choices  = $users->map(fn (AuthUser $u) => "{$u->name} <{$u->email}>")->toArray();
        $selected = (string) $this->choice('対象ユーザーを選択してください', $choices);
        preg_match('/<(.+)>/', $selected, $matches);

        return $matches[1] ?? null;
    }

    private function resolveAuthority(): ?EUserAuthority
    {
        $authorityOption = $this->option('authority');

        if (is_string($authorityOption)) {
            $authority = $this->parseAuthorityName($authorityOption);
            if ($authority === null) {
                $this->error("不正な権限名です: {$authorityOption}");
                $this->line('有効な値: ADMIN_PRIVILEGE, ADMIN_COMMAND, ROOT_PRIVILEGE');
            }

            return $authority;
        }

        $choices         = ['ADMIN_PRIVILEGE', 'ADMIN_COMMAND', 'ROOT_PRIVILEGE'];
        $selected        = (string) $this->choice('付与する権限を選択してください', $choices, 0);

        return $this->parseAuthorityName($selected);
    }

    private function parseAuthorityName(string $name): ?EUserAuthority
    {
        return match (strtoupper($name)) {
            'ADMIN_PRIVILEGE' => EUserAuthority::ADMIN_PRIVILEGE,
            'ADMIN_COMMAND'   => EUserAuthority::ADMIN_COMMAND,
            'ROOT_PRIVILEGE'  => EUserAuthority::ROOT_PRIVILEGE,
            default           => null,
        };
    }
}

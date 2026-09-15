<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Facades\Models\App\Trn\TrnUserRewardService;
use App\Models\App\Auth\AuthUser;
use App\Models\App\Trn\TrnUserAuthority;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;
use Inertia\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected ?AuthUser $authUser = null;

    /**
     * Inertiaを介してhtmlを作成
     *
     * @note ログイン情報など、共通のパラメータを処理.
     *
     * @param  array<mixed>  $props
     */
    protected function render(
        string $component,
        array $props = []
    ): Response {
        // ローカルで処理が早すぎないように調整する.
        localSleep();

        return Inertia::render($component, array_merge(
            [
                'csrfToken'            => csrf_token(),
                'authUser'             => fn () => $this->getAuthUser([
                    'TrnUser',
                ]),
                'location'             => fn () => $this->getLocation(),
                'trnUserAuthorityList' => fn () => $this->getUserAuthorityList(),
                'achievementCount'     => fn () => TrnUserRewardService::countAchievable($this->authUser),
            ],
            $props,
        ));
    }

    /**
     * 認証ユーザー情報の取得.
     *
     * @param  array<mixed>  $with
     * @return array<mixed>|null
     */
    private function getAuthUser(array $with = []): ?array
    {
        /** @var AuthUser|null $authUser */
        $authUser       = AuthUser::query()
            ->with($with)
            ->find(Auth::id());
        $this->authUser = $authUser;

        return $this->authUser?->toPayload($with);
    }

    /**
     * ロケーションの取得.
     *
     * @return array<string>
     */
    private function getLocation(): array
    {
        return [
            'current' => Request::url(),
            'path'    => Request::path(),
        ];
    }

    /**
     * @param  array<mixed>  $with
     * @return array<mixed>
     */
    private function getUserAuthorityList(array $with = []): array
    {
        // TODO: サービスへ移動
        /** @var Collection<int, TrnUserAuthority> $trnUserAuthorityList */
        $trnUserAuthorityList = TrnUserAuthority::query()
            ->with($with)
            ->where('trn_user_id', $this->authUser?->TrnUser?->id)
            ->alive()
            ->get();

        return $trnUserAuthorityList->map(
            fn (TrnUserAuthority $trnUserAuthority) => $trnUserAuthority->toPayload($with)
        )->toArray();
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Models\App\Trn;

use App\Constant;
use App\Enum\App\EArchiveLevel;
use App\Enum\App\EPipeKind;
use App\Facades\Internal\ActivityLogService;
use App\Facades\Internal\PipeService;
use App\Models\App\Auth\AuthUser;
use App\Models\App\Trn\TrnUser;
use App\Pipe\App\PipeAppPersonalSettingEditUpdate;
use App\Pipe\App\PipeAppRewardAchieve;
use App\Pipe\App\PipeAppShopLunchTicket;
use App\Pipe\App\PipeAppUserCreate;
use App\Pipe\App\PipeAppUserDelete;
use App\Pipe\PipeBase;
use App\Services\Models\ModelServiceBase;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Throwable;

class TrnUserService extends ModelServiceBase
{
    const string IMAGE_CACHE_URL_KEY     = 'image_url_';

    const int IMAGE_CACHE_HOUR           = 1;

    const int IMAGE_CACHE_TTL            = self::IMAGE_CACHE_HOUR * 60 * 60;

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
                EPipeKind::APP_SHOP_LUNCH_TICKET            => $this->execAppShopLunchTicket($pipe),
                EPipeKind::APP_REWARD_ACHIEVE               => $this->execAppRewardAchieve($pipe),
                EPipeKind::APP_PERSONAL_SETTING_EDIT_UPDATE => $this->execAppPersonalSettingEditUpdate($pipe),
                EPipeKind::APP_USER_CREATE                  => $this->execAppUserCreate($pipe),
                EPipeKind::APP_USER_DELETE                  => $this->execAppUserDelete($pipe),
                default                                     => throw new Exception,
            };
            $pipe->onUpdate(__CLASS__);
        }
    }

    /**
     * ランチチケット予約.
     *
     * @throws Exception|Throwable
     */
    private function execAppShopLunchTicket(
        PipeBase $pipe
    ): void {
        /** @var PipeAppShopLunchTicket $pipe */

        // 所持金額確認.
        $trnUser = $this->findByIdOrFail($pipe->authUser?->TrnUser?->id ?: 0);
        if ($trnUser->current_gold < $pipe->mstGoods?->price) {
            throw new Exception('not enough gold');
        }

        // 所持金額の減算.
        $trnUser->current_gold -= $pipe->mstGoods?->price;

        // ログ出力.
        ActivityLogService::changeModel(
            $pipe->authUser,
            __CLASS__,
            __FUNCTION__,
            (string) __LINE__,
            $trnUser,
            [
                'goodsId' => $pipe->mstGoods?->id,
            ]
        );

        // 保存.
        $this->updateOrFail($trnUser);
    }

    /**
     * 報酬の達成.
     *
     * @throws Exception|Throwable
     */
    private function execAppRewardAchieve(
        PipeBase $pipe
    ): void {
        /** @var PipeAppRewardAchieve $pipe */

        // GOLDの獲得.
        $trnUser = $this->findByIdOrFail($pipe->authUser?->TrnUser?->id ?: 0);
        $trnUser->accumulation_gold += $pipe->achieveGold;
        $trnUser->current_gold      += $pipe->achieveGold;

        // 保存.
        $this->updateOrFail($trnUser);
    }

    /**
     * @throws Throwable
     */
    private function execAppPersonalSettingEditUpdate(
        PipeBase $pipe
    ): void {
        /** @var PipeAppPersonalSettingEditUpdate $pipe */

        // 個人設定の更新.
        $trnUser                    = $this->findByIdOrFail($pipe->trnUserId);
        $trnUser->nickname          = $pipe->nickname;
        $trnUser->birth_date        = $pipe->birthDate;
        $trnUser->self_introduction = $pipe->selfIntroduction;

        // 画像の更新.
        if ($pipe->upload !== null) {
            // 古い画像の削除.
            Storage::disk('storage')->delete($trnUser->face_image_path);

            // 画像の登録処理.
            $ext                      = $pipe->upload->extension();
            $name                     = "$trnUser->id.$ext";
            Storage::disk('storage')->putFileAs(Constant::FACE_IMAGE_S3_PATH, $pipe->upload, $name);

            // ファイルパスの更新.
            $trnUser->face_image_path = Constant::FACE_IMAGE_S3_PATH.$name;

            // キャッシュの削除.
            $cacheKey                 = $this->getCacheKeyFaceImage($trnUser);
            Cache::delete($cacheKey);
        }

        // 保存.
        $this->updateOrFail($trnUser);
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    private function execAppUserCreate(
        PipeBase $pipe
    ): void {
        /** @var PipeAppUserCreate $pipe */
        if (is_null($pipe->createdAuthUser)) {
            throw new Exception('invalid auth user');
        }

        // ユーザーの作成.
        $this->createNewUser(
            $pipe->createdAuthUser,
            $pipe->nickname,
        );
    }

    /**
     * @throws Exception
     */
    private function execAppUserDelete(
        PipeBase $pipe
    ): void {
        /** @var PipeAppUserDelete $pipe */

        // 対象データを取得.
        $target                  = $this->findByIdOrFail($pipe->trnUserId);

        // 対象データを削除.
        $target->e_archive_level = EArchiveLevel::ARCHIVE->value;

        // 削除を保存.
        $this->updateOrFail($target);
    }

    /**
     * オフセットページネーション情報の取得.
     *
     * @param  array<mixed>  $with
     * @return array<string, mixed>
     */
    public function offsetPagination(
        int $index,
        int $step,
        array $with = [],
        string $keyword = '',
        bool $inactiveUserFlag = false,
    ): array {
        // リスト取得.
        /** @var Collection<int, TrnUser> $trnUserList */
        $trnUserList      = TrnUser::query()
            ->when(! empty($keyword), function ($query) use ($keyword) {
                // 検索文字列があれば名称から検索.
                return $query->where('nickname', 'like', "%$keyword%");
            })
            ->when(! empty($inactiveUserFlag), function ($query) {
                // 非アクティブユーザーも含める.
                return $query->whereDoesntHave('TrnAttendanceState', function ($query) {
                    return $query->whereRaw('created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)');
                });
            })
            ->with($with)
            ->alive()
            ->offset($index * $step)
            ->limit($step)
            ->get();

        // カウント取得.
        $trnUserListCount = TrnUser::query()
            ->when(! empty($keyword), function ($query) use ($keyword) {
                // 検索文字列があれば名称から検索.
                return $query->where('nickname', 'like', "%$keyword%");
            })
            ->when(! empty($inactiveUserFlag), function ($query) {
                // 非アクティブユーザーも含める.
                return $query->whereDoesntHave('TrnAttendanceState', function ($query) {
                    return $query->whereRaw('created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)');
                });
            })
            ->alive()
            ->count();

        return [
            'list'  => $trnUserList->map(function (TrnUser $trnUser) use ($with) {
                return $trnUser->toPayload($with);
            })->toArray(),
            'count' => $trnUserListCount,
        ];
    }

    /**
     * 全て取得.
     *
     * @param  array<string>  $with
     * @return Collection<int, TrnUser>
     */
    public function list(array $with = []): Collection
    {
        /** @var Collection<int, TrnUser> */
        return TrnUser::query()
            ->with($with)
            ->alive()
            ->get();
    }

    /**
     * IDで検索(orFail).
     *
     * @param  array<mixed>  $with
     */
    public function findByIdOrFail(int $id, array $with = []): TrnUser
    {
        /** @var TrnUser */
        return TrnUser::query()
            ->with($with)
            ->alive()
            ->findOrFail($id);
    }

    /**
     * 認証ユーザーIDで検索.
     */
    public function findByAuthId(int $authId): ?TrnUser
    {
        /** @var TrnUser|null */
        return TrnUser::query()
            ->where('auth_id', $authId)
            ->alive()
            ->first();
    }

    /**
     * 認証ユーザーIDで検索.
     */
    public function findByAuthIdOrFail(int $authId): TrnUser
    {
        /** @var TrnUser */
        return TrnUser::query()
            ->where('auth_id', $authId)
            ->alive()
            ->firstOrFail();
    }

    /**
     * ユーザーの画像URLを取得する.
     */
    public function getImageUrl(TrnUser $trnUser): string
    {
        // キャッシュから取得する.
        $cacheKey = $this->getCacheKeyFaceImage($trnUser);

        return (string) Cache::remember($cacheKey, self::IMAGE_CACHE_TTL, function () use ($trnUser) {
            // URLを発行.
            return $this->createProfileImageTemporaryUrl($trnUser, self::IMAGE_CACHE_HOUR);
        });
    }

    /**
     * 画像パスを保存するキャッシュのキー.
     */
    private function getCacheKeyFaceImage(TrnUser $trnUser): string
    {
        return self::IMAGE_CACHE_URL_KEY.$trnUser->id;
    }

    /**
     * プロフィール画像用の一時アクセス用URLを作成する.
     */
    public function createProfileImageTemporaryUrl(TrnUser $trnUser, int $hour): string
    {
        $imagePath  = $trnUser->face_image_path;
        $expiration = Carbon::now()->addHours($hour);

        return Storage::disk('storage')
            ->temporaryUrl(
                $imagePath,
                $expiration
            );
    }

    /**
     * 認証成功時にユーザーの作成を行う
     *
     *
     * @throws Throwable
     */
    public function createNewUser(AuthUser $authUser, string $temporaryName, int $id = 0): TrnUser
    {
        // 新規にユーザーを作成.
        $trnUser                  = new TrnUser;
        if ($id !== 0) {
            $trnUser->id   = $id;
        }
        $trnUser->auth_id         = $authUser->id;
        $trnUser->nickname        = $temporaryName;
        $trnUser->saveOrFail();

        // 初期画像を保存.
        $defaultPath              = $this->createDefaultFaceImagePath();
        $file                     = Storage::disk('local')->get($defaultPath);
        if (! $file) {
            throw new Exception('default image not found');
        }
        $ext                      = pathinfo($defaultPath, PATHINFO_EXTENSION);
        $path                     = Constant::FACE_IMAGE_S3_PATH."$trnUser->id.$ext";
        Storage::disk('storage')->put($path, $file);

        // 画像パスを保存.
        $trnUser->face_image_path = $path;
        $trnUser->saveOrFail();

        return $trnUser;
    }

    /**
     * 再認証による復帰.
     *
     * @throws Exception
     */
    public function comebackUser(AuthUser $authUser): void
    {
        // ユーザー情報を取得.
        $trnUser                  = $this->findByAuthIdOrFail($authUser->id);

        // アーカイブレベルを変更.
        $trnUser->e_archive_level = EArchiveLevel::ALIVE->value;

        // 保存.
        $this->updateOrFail($trnUser);
    }

    /**
     * 初期イメージのパス取得.
     *
     * @throws Exception
     */
    public function createDefaultFaceImagePath(): string
    {
        $index = random_int(Constant::FACE_IMAGE_DEFAULT_INDEX_MIN, Constant::FACE_IMAGE_DEFAULT_INDEX_MAX);

        return sprintf(Constant::FACE_IMAGE_DEFAULT, $index);
    }
}

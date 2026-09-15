<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Constant;
use App\Enum\App\GoodJob\ETargetType;
use App\Enum\App\ShuffleLunch\EEventTimeZone;
use App\Facades\Models\App\Trn\TrnUserService;
use App\Models\App\Auth\AuthUser;
use App\Models\App\Trn\TrnDivision;
use App\Models\App\Trn\TrnDivisionUser;
use App\Models\App\Trn\TrnGoodJob;
use App\Models\App\Trn\TrnProject;
use App\Models\App\Trn\TrnProjectUser;
use App\Models\App\Trn\TrnShuffleLunchEntry;
use App\Models\App\Trn\TrnUser;
use App\Models\App\Trn\TrnUserDivisionPriority;
use App\Models\App\Trn\TrnUserProjectPriority;
use App\Models\App\Trn\TrnUserReward;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Random\RandomException;
use Symfony\Component\Console\Helper\ProgressBar;
use Throwable;

class TestCaseSeeder extends Seeder
{
    private ?ProgressBar $pb    = null;

    private string $pbModelName = '';

    /**
     *  ローカル/テストケース向けの初期データを用意する.
     *
     * @throws RandomException
     * @throws Throwable
     */
    public function run(): void
    {
        $this->command->info('******* initialize local/test data. ********');

        // local/tst/dev環境でなければ初期化実行不可.
        if (! (isLocal() || isTst() || isDev())) {
            $this->command->info('!!! caution local/tst/dev environment only. !!!');

            return;
        }

        Schema::disableForeignKeyConstraints();

        try {
            // データの削除.
            AuthUser::truncate();
            TrnUser::truncate();
            TrnDivision::truncate();
            TrnDivisionUser::truncate();
            TrnProject::truncate();
            TrnProjectUser::truncate();
            TrnUserReward::truncate();
            TrnShuffleLunchEntry::truncate();
            TrnGoodJob::truncate();

            $instance     = $this;

            // 認証ユーザー作成.
            $this->beginFactory('AuthUser', 150);
            AuthUser::factory(150)
                ->sequence(function (Sequence $sequence) use ($instance) {
                    $count = $sequence->index + 1;
                    $instance->advanceFactory();

                    return [
                        'email' => "wportal2-dummy-$count@919dummy.jp",
                    ];
                })
                ->create([
                    'password'  => substr(bin2hex(random_bytes(16)), 0, 16),
                    'google_id' => '',
                ]);
            $this->endFactory();

            // ユーザー作成.
            $this->beginFactory('TrnUser', 150);
            /** @var Collection<int, AuthUser> $authUserList */
            $authUserList = AuthUser::query()->get();
            $authUserList->each(function (AuthUser $authUser) use ($instance) {
                // 対応するユーザーを作成.
                TrnUser::factory()
                    ->create([
                        'nickname' => $authUser->name,
                        'auth_id'  => $authUser->id,
                    ]);

                $instance->advanceFactory();
            });
            $this->endFactory();

            // ユーザー画像更新.
            $this->beginFactory('fix face image', 150);
            /** @var Collection<int, TrnUser> $trnUserList */
            $trnUserList  = TrnUser::query()->get();
            $trnUserList->each(function (TrnUser $trnUser) use ($instance) {
                // 初期画像を保存.
                $defaultPath              = TrnUserService::createDefaultFaceImagePath();
                $file                     = Storage::disk('local')->get($defaultPath);
                if (! $file) {
                    return;
                }

                $ext                      = pathinfo($defaultPath, PATHINFO_EXTENSION);
                $path                     = Constant::FACE_IMAGE_S3_PATH."$trnUser->id.$ext";
                Storage::disk('storage')->put($path, $file);

                // 画像パスを保存.
                $trnUser->face_image_path = $path;
                $trnUser->saveOrFail();

                $instance->advanceFactory();
            });
            $instance->endFactory();

            // プロジェクト・課優先度.
            $instance->beginFactory('priority', 150);
            $trnUserList->each(function (TrnUser $trnUser) use ($instance) {
                // プロジェクト優先度作成.
                TrnUserProjectPriority::factory(10)
                    ->sequence(function (Sequence $sequence) {
                        return [
                            'trn_project_id' => $sequence->index + 1,
                        ];
                    })
                    ->create([
                        'trn_user_id' => $trnUser->id,
                    ]);

                // 課優先度作成.
                TrnUserDivisionPriority::factory(10)
                    ->sequence(function (Sequence $sequence) {
                        return [
                            'trn_division_id' => $sequence->index + 1,
                        ];
                    })
                    ->create([
                        'trn_user_id' => $trnUser->id,
                    ]);

                $instance->advanceFactory();
            });
            $instance->endFactory();

            // 課作成.
            $this->beginFactory('TrnDivision', 20);
            TrnDivision::factory(20)
                ->create()->each(static function (TrnDivision $trnDivision) use ($instance) {
                    for ($i = (($trnDivision->id-1) * 20); $i < $trnDivision->id * 20; $i++) {
                        TrnDivisionUser::factory()->create([
                            'trn_division_id' => $trnDivision->id,
                            'trn_user_id'     => $i + 1,
                        ]);
                    }

                    $instance->advanceFactory();
                });

            $this->endFactory();

            // プロジェクト作成.
            $this->beginFactory('TrnProject', 20);
            TrnProject::factory(20)
                ->create()->each(function (TrnProject $trnProject) use ($instance) {
                    for ($i = (($trnProject->id-1) * 20); $i < $trnProject->id * 20; $i++) {
                        TrnProjectUser::factory()->create([
                            'trn_project_id'  => $trnProject->id,
                            'trn_user_id'     => $i + 1,
                        ]);
                    }
                    $instance->advanceFactory();
                });

            $this->endFactory();

            // ランチエントリー.
            $this->beginFactory('TrnShuffleLunchEntry', 40);
            TrnShuffleLunchEntry::factory(40)
                ->sequence(function (Sequence $sequence) use ($instance) {
                    $index = $sequence->index + 1;
                    $instance->advanceFactory();

                    return [
                        'trn_user_id'     => $index,
                        'event_time_zone' => $index % 2 === 0 ? EEventTimeZone::EARLY->value : EEventTimeZone::NORMAL->value,
                    ];
                })->create([
                    'event_date'      => now(),
                ]);
            $this->endFactory();

            // GoodJob.
            $this->beginFactory('TrnGoodJob', 40);

            // GoodJob: ユーザー.
            TrnGoodJob::factory(10)
                ->sequence(function (Sequence $sequence) use ($instance) {
                    $index = $sequence->index + 1;
                    $type  = ETargetType::USER->value;
                    $instance->advanceFactory();

                    return [
                        'trn_user_id'      => $index,
                        'from_trn_user_id' => $index,
                        'to_trn_user_id'   => $index + 10,
                        'title'            => "title-$type-$index",
                        'content'          => "content-$type-$index\ncontent-$type-$index\ncontent-$type-$index",
                    ];
                })->create([
                    'from_target_type'     => ETargetType::USER->value,
                    'to_target_type'       => ETargetType::USER->value,
                ]);

            // GoodJob: 課.
            TrnGoodJob::factory(10)
                ->sequence(function (Sequence $sequence) use ($instance) {
                    $index = $sequence->index + 1;
                    $type  = ETargetType::USER->value;
                    $instance->advanceFactory();

                    return [
                        'trn_user_id'          => $index,
                        'from_trn_division_id' => $index,
                        'to_trn_division_id'   => $index + 10,
                        'title'                => "title-$type-$index",
                        'content'              => "content-$type-$index\ncontent-$type-$index\ncontent-$type-$index",
                    ];
                })->create([
                    'from_target_type'     => ETargetType::DIVISION->value,
                    'to_target_type'       => ETargetType::DIVISION->value,
                ]);

            // GoodJob: プロジェクト.
            TrnGoodJob::factory(10)
                ->sequence(function (Sequence $sequence) use ($instance) {
                    $index = $sequence->index + 1;
                    $type  = ETargetType::USER->value;
                    $instance->advanceFactory();

                    return [
                        'trn_user_id'         => $index,
                        'from_trn_project_id' => $index,
                        'to_trn_project_id'   => $index + 10,
                        'title'               => "title-$type-$index",
                        'content'             => "content-$type-$index\ncontent-$type-$index\ncontent-$type-$index",
                    ];
                })->create([
                    'from_target_type'     => ETargetType::PROJECT->value,
                    'to_target_type'       => ETargetType::PROJECT->value,
                ]);

            // GoodJob: その他.
            TrnGoodJob::factory(10)
                ->sequence(function (Sequence $sequence) use ($instance) {
                    $index = $sequence->index + 1;
                    $type  = ETargetType::USER->value;
                    $instance->advanceFactory();

                    return [
                        'trn_user_id'      => $index,
                        'from_other_label' => "other-$index",
                        'to_other_label'   => "other-$index",
                        'title'            => "title-$type-$index",
                        'content'          => "content-$type-$index\ncontent-$type-$index\ncontent-$type-$index",
                    ];
                })->create([
                    'from_target_type'     => ETargetType::LABEL->value,
                    'to_target_type'       => ETargetType::LABEL->value,
                ]);

            $this->endFactory();
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        $this->command->info('******* initialize done. ********');
    }

    /**
     * 生成ブロックの開始.
     */
    private function beginFactory(string $modelName, int $count): void
    {
        $this->pbModelName = $modelName;
        $this->command->info("begin: $this->pbModelName");
        $this->pb          = $this->command->getOutput()->createProgressBar($count);
    }

    /**
     * 生成ブロックの進行.
     */
    private function advanceFactory(): void
    {
        $this->pb?->advance();
    }

    /**
     * 生成ブロックの終了.
     */
    private function endFactory(): void
    {
        $this->pb?->finish();
        $this->command->info(" done: $this->pbModelName");
    }
}

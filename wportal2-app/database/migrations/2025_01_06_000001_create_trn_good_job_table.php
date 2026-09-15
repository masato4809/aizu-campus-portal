<?php

declare(strict_types=1);

use App\Enum\App\EArchiveLevel;
use App\Enum\App\GoodJob\ETargetType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const TABLE_NAME = 'trn_good_job';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();

            // 作成者.
            $table->integer('trn_user_id')->comment('作成者ユーザID');

            // 発信元情報.
            $table->integer('from_target_type')
                ->default(ETargetType::INVALID->value)
                ->comment('発信元種別');
            $table->integer('from_trn_user_id')->comment('発信元ユーザID');
            $table->integer('from_trn_division_id')->comment('発信元部署ID');
            $table->integer('from_trn_project_id')->comment('発信元プロジェクトID');
            $table->string('from_other_label', 255)->comment('発信元ラベル名');

            // 発信先情報.
            $table->integer('to_target_type')
                ->default(ETargetType::INVALID->value)
                ->comment('発信先種別');
            $table->integer('to_trn_user_id')->comment('発信先ユーザID');
            $table->integer('to_trn_division_id')->comment('発信先部署ID');
            $table->integer('to_trn_project_id')->comment('発信先プロジェクトID');
            $table->string('to_other_label', 255)->comment('発信先ラベル名');

            // GoodJob内容.
            $table->string('title', 255)->comment('タイトル');
            $table->text('content')->comment('内容');

            $table->integer('e_archive_level')->default(EArchiveLevel::ALIVE->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};

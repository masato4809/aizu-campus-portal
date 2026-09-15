<?php

declare(strict_types=1);

use App\Enum\App\EArchiveLevel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const TABLE_NAME = 'trn_shuffle_lunch_group';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->date('event_date')->comment('ランチ日付');
            $table->integer('event_time_zone')->comment('ランチ時間幅');
            $table->integer('group_id')->comment('グループID');
            $table->integer('trn_user_id')->comment('ユーザID');
            $table->integer('rakumo_blocked')->default(0)->comment('rakumoがブロック済みかどうか');
            $table->integer('e_archive_level')->default(EArchiveLevel::ALIVE->value);
            $table->timestamps();

            $table->index('event_date');
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

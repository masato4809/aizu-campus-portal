<?php

declare(strict_types=1);

use App\Enum\App\EArchiveLevel;
use App\Enum\App\EEnableFlag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const TABLE_NAME = 'mst_reward';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->integer('reward_kind');
            $table->integer('reward_rank');
            $table->string('reward_name');
            $table->string('reward_explain');
            $table->integer('reward_gold')->default(0);
            $table->integer('e_enable_repeat')->default(EEnableFlag::INVALID->value);
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

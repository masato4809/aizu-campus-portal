<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const TABLE_NAME = 'trn_user_reward';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            $table->dateTime('achieved_at')->nullable()->after('received_count');
            $table->dateTime('received_at')->nullable()->after('achieved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn('achieved_at');
            $table->dropColumn('received_at');
        });
    }
};

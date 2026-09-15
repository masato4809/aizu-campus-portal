<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const TABLE_NAME = 'trn_user';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            $table->integer('current_gold')->default(0)->after('nickname');
            $table->integer('accumulation_gold')->default(0)->after('current_gold');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn('current_gold');
            $table->dropColumn('accumulation_gold');
        });
    }
};

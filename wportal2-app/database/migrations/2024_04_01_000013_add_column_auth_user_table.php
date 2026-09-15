<?php

declare(strict_types=1);

use App\Enum\App\EArchiveLevel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const TABLE_NAME = 'auth_user';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            $table->integer('e_archive_level')->default(EArchiveLevel::ALIVE->value)->after('onetime_password_expired');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn('e_archive_level');
        });
    }
};

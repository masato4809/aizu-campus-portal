<?php

declare(strict_types=1);

use App\Enum\App\EEnableFlag;
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
            $table->integer('e_enable_sp_login')->default(EEnableFlag::INVALID->value)->after('onetime_password_expired');
            $table->string('sp_password', 128)->nullable()->after('e_enable_sp_login');
            $table->string('sp_google_2fa_secret', 128)->nullable()->after('sp_password');
            $table->integer('sp_login_failed_count')->default(0)->after('sp_google_2fa_secret');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn('e_enable_sp_login');
            $table->dropColumn('sp_password');
            $table->dropColumn('sp_google_2fa_secret');
            $table->dropColumn('sp_login_failed_count');
        });
    }
};

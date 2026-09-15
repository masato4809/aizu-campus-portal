<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campus_clubs', function (Blueprint $table) {
            if (! Schema::hasColumn('campus_clubs', 'contact_url')) {
                $table->string('contact_url', 2048)->nullable()->after('body');
            }
        });
    }

    public function down(): void
    {
        Schema::table('campus_clubs', function (Blueprint $table) {
            if (Schema::hasColumn('campus_clubs', 'contact_url')) {
                $table->dropColumn('contact_url');
            }
        });
    }
};

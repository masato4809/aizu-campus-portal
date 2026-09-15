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
            if (! Schema::hasColumn('campus_clubs', 'website_url')) {
                $table->string('website_url', 2048)->nullable()->after('contact_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('campus_clubs', function (Blueprint $table) {
            if (Schema::hasColumn('campus_clubs', 'website_url')) {
                $table->dropColumn('website_url');
            }
        });
    }
};

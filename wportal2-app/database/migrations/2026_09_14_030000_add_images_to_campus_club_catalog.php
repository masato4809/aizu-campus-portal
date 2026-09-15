<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campus_club_catalog', function (Blueprint $table): void {
            $table->string('image_path', 255)->nullable()->after('source_url');
            $table->string('image_mime_type', 40)->nullable()->after('image_path');
            $table->unsignedInteger('image_width')->nullable()->after('image_mime_type');
            $table->unsignedInteger('image_height')->nullable()->after('image_width');
        });
    }

    public function down(): void
    {
        Schema::table('campus_club_catalog', function (Blueprint $table): void {
            $table->dropColumn(['image_path', 'image_mime_type', 'image_width', 'image_height']);
        });
    }
};

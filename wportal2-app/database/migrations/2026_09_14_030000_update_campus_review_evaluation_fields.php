<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campus_reviews', function (Blueprint $table) {
            $table->unsignedTinyInteger('test_weight')->nullable()->after('evaluation_methods');
            $table->unsignedTinyInteger('report_weight')->nullable()->after('test_weight');
            $table->unsignedTinyInteger('attendance_weight')->nullable()->after('report_weight');
        });

        // Keep legacy review values: adding weights must not delete existing data.
    }

    public function down(): void
    {
        Schema::table('campus_reviews', function (Blueprint $table) {
            $table->dropColumn(['test_weight', 'report_weight', 'attendance_weight']);
        });
    }
};

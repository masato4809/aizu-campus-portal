<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campus_students', function (Blueprint $table) {
            $table->unsignedInteger('past_exam_count')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('campus_students', function (Blueprint $table) {
            $table->dropColumn('past_exam_count');
        });
    }
};

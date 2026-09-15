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
            // Nullable for existing reviews: unknown is different from zero/none.
            $table->string('faculty', 120)->nullable();
            $table->string('department', 120)->nullable();
            $table->string('attendance_method', 200)->nullable();
            $table->unsignedTinyInteger('assignment_amount')->nullable();
            $table->unsignedTinyInteger('remote_percentage')->nullable();
            $table->text('materials')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('campus_reviews', function (Blueprint $table) {
            $table->dropColumn(['faculty', 'department', 'attendance_method', 'assignment_amount', 'remote_percentage', 'materials']);
        });
    }
};

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
            $table->string('learning_style', 20)->nullable()->after('personality');
            $table->string('goal_orientation', 20)->nullable()->after('learning_style');
        });
    }

    public function down(): void
    {
        Schema::table('campus_students', function (Blueprint $table) {
            $table->dropColumn(['learning_style', 'goal_orientation']);
        });
    }
};

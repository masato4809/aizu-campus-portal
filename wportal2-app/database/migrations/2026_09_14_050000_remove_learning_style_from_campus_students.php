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
            if (Schema::hasColumn('campus_students', 'learning_style')) {
                $table->dropColumn('learning_style');
            }
        });
    }

    public function down(): void
    {
        Schema::table('campus_students', function (Blueprint $table) {
            $table->string('learning_style', 20)->nullable()->after('personality');
        });
    }
};

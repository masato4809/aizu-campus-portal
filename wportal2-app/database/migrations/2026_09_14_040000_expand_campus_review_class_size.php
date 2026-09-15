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
            $table->string('class_size', 30)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Keep the wider column to avoid truncating saved range_20_39 values.
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_club_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('campus_clubs')->cascadeOnDelete();
            $table->string('path');
            $table->string('mime_type', 32);
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
        });
        Schema::table('campus_students', function (Blueprint $table) {
            $table->index(['university', 'circle'], 'campus_students_university_circle_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_club_images');
        Schema::table('campus_students', function (Blueprint $table) {
            $table->dropIndex('campus_students_university_circle_index');
        });
    }
};

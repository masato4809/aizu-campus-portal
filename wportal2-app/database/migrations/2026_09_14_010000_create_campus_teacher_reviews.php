<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_teacher_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('campus_teachers')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('campus_students')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('body');
            $table->timestamps();
            $table->unique(['teacher_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_teacher_reviews');
    }
};

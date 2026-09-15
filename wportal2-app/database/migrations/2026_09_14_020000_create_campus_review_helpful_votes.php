<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_review_helpful_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('campus_reviews')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('campus_students')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['review_id', 'student_id'], 'campus_review_helpful_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_review_helpful_votes');
    }
};

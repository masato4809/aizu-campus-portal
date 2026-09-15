<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_students', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('email')->unique();
            $table->string('password');
            $table->string('university', 120);
            $table->string('faculty', 120);
            $table->unsignedTinyInteger('year');
            $table->string('circle', 120)->default('');
            $table->string('personality', 4)->nullable();
            $table->timestamps();
        });
        Schema::create('campus_courses', function (Blueprint $table) {
            $table->id();
            $table->string('university', 120);
            $table->string('name', 120);
            $table->string('professor', 80);
            $table->unique(['university', 'name', 'professor']);
            $table->timestamps();
        });
        Schema::create('campus_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('campus_students')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('campus_courses')->cascadeOnDelete();
            foreach (['clarity', 'interest', 'ease', 'workload', 'recommendation'] as $rating) {
                $table->unsignedTinyInteger($rating);
            }
            $table->text('body');
            $table->unique(['student_id', 'course_id']);
            $table->timestamps();
        });
        Schema::create('campus_clubs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('campus_students')->cascadeOnDelete();
            $table->string('name', 120);
            $table->text('body');
            $table->timestamps();
        });
        Schema::create('campus_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('campus_students')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('campus_students')->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['campus_messages', 'campus_clubs', 'campus_reviews', 'campus_courses', 'campus_students'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};

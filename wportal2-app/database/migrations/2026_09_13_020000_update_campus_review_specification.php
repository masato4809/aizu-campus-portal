<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campus_reviews', function (Blueprint $table) {
            // Preserve old scores without treating them as the new evaluation axes.
            foreach (['clarity', 'interest', 'ease', 'workload', 'recommendation'] as $column) {
                $table->unsignedTinyInteger($column)->nullable()->change();
            }

            $columns = [
                'username'             => fn () => $table->string('username', 80)->nullable(),
                'user_type'            => fn () => $table->string('user_type', 30)->nullable(),
                'has_past_exam'        => fn () => $table->boolean('has_past_exam')->nullable(),
                'evaluation_methods'   => fn () => $table->json('evaluation_methods')->nullable(),
                'assignment_load'      => fn () => $table->string('assignment_load', 10)->nullable(),
                'remote_level'         => fn () => $table->string('remote_level', 10)->nullable(),
                'materials_url'        => fn () => $table->string('materials_url', 2048)->nullable(),
                'class_size'           => fn () => $table->string('class_size', 10)->nullable(),
                'academic_year'        => fn () => $table->unsignedSmallInteger('academic_year')->nullable(),
                'semester'             => fn () => $table->string('semester', 10)->nullable(),
                'credit_ease'          => fn () => $table->unsignedTinyInteger('credit_ease')->nullable(),
                'grade_ease'           => fn () => $table->unsignedTinyInteger('grade_ease')->nullable(),
                'assignment_lightness' => fn () => $table->unsignedTinyInteger('assignment_lightness')->nullable(),
                'satisfaction'         => fn () => $table->unsignedTinyInteger('satisfaction')->nullable(),
                'past_exam_similarity' => fn () => $table->unsignedTinyInteger('past_exam_similarity')->nullable(),
            ];

            foreach ($columns as $name => $definition) {
                if (! Schema::hasColumn('campus_reviews', $name)) {
                    $definition();
                }
            }
        });

        DB::table('campus_reviews')->orderBy('id')->chunkById(200, function ($reviews) {
            foreach ($reviews as $review) {
                $name = DB::table('campus_students')->where('id', $review->student_id)->value('name');
                DB::table('campus_reviews')->where('id', $review->id)->update(['username' => $name]);
            }
        });
        // Rename this development community while keeping account and course IDs.
        DB::transaction(function () {
            DB::table('campus_students')->where('university', '金沢大学')->update(['university' => '会津大学']);
            DB::table('campus_courses')->where('university', '金沢大学')->update(['university' => '会津大学']);
        });
    }

    public function down(): void
    {
        Schema::table('campus_reviews', function (Blueprint $table) {
            $columns  = ['username', 'user_type', 'has_past_exam', 'evaluation_methods', 'assignment_load', 'remote_level', 'materials_url', 'class_size', 'academic_year', 'semester', 'credit_ease', 'grade_ease', 'assignment_lightness', 'satisfaction', 'past_exam_similarity'];
            $existing = array_values(array_filter($columns, fn (string $column): bool => Schema::hasColumn('campus_reviews', $column)));

            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
        // University names remain Aizu and legacy scores remain nullable to avoid fabricating data.
    }
};

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
        Schema::table('campus_courses', function (Blueprint $table) {
            if (Schema::hasIndex('campus_courses', 'campus_courses_university_name_professor_unique')) {
                $table->dropUnique('campus_courses_university_name_professor_unique');
            }
            $table->string('name', 255)->change();
            $table->text('professor')->change(); // Compatibility display for existing views.
            $columns = [
                'academic_year'     => fn () => $table->unsignedSmallInteger('academic_year')->nullable(),
                'course_code'       => fn () => $table->string('course_code', 64)->nullable(),
                'name_en'           => fn () => $table->string('name_en', 255)->nullable(),
                'semester'          => fn () => $table->text('semester')->nullable(),
                'syllabus_url'      => fn () => $table->text('syllabus_url')->nullable(),
                'description'       => fn () => $table->text('description')->nullable(),
                'offerings'         => fn () => $table->json('offerings')->nullable(),
                'source_fetched_at' => fn () => $table->timestamp('source_fetched_at')->nullable(),
                'legacy_key'        => fn () => $table->string('legacy_key', 64)->nullable(),
            ];

            foreach ($columns as $name => $definition) {
                if (! Schema::hasColumn('campus_courses', $name)) {
                    $definition();
                }
            }

            if (! Schema::hasIndex('campus_courses', 'campus_official_course_unique')) {
                $table->unique(['university', 'academic_year', 'course_code'], 'campus_official_course_unique');
            }
            if (! Schema::hasIndex('campus_courses', 'campus_legacy_course_unique')) {
                $table->unique(['university', 'legacy_key'], 'campus_legacy_course_unique');
            }
        });
        DB::table('campus_courses')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                DB::table('campus_courses')->where('id', $row->id)->update([
                    'legacy_key' => hash('sha256', $row->name."\0".$row->professor),
                ]);
            }
        });
        if (! Schema::hasTable('campus_teachers')) {
            Schema::create('campus_teachers', function (Blueprint $table) {
                $table->id();
                $table->string('university', 120);
                $table->string('external_teacher_id', 64)->nullable();
                $table->string('source_key', 64);
                $table->string('name', 255);
                $table->string('name_en', 255)->nullable();
                $table->string('email', 255)->nullable();
                $table->text('department')->nullable();
                $table->text('position')->nullable();
                $table->text('research_field')->nullable();
                $table->text('laboratory_name')->nullable();
                $table->text('profile_url');
                $table->text('laboratory_url')->nullable();
                $table->text('personal_url')->nullable();
                $table->json('aliases')->nullable();
                $table->timestamp('source_fetched_at')->nullable();
                $table->timestamps();
                $table->unique(['university', 'source_key'], 'campus_teacher_source_unique');
                $table->unique(['university', 'external_teacher_id'], 'campus_teacher_external_unique');
            });
        }
        if (! Schema::hasTable('campus_course_teachers')) {
            Schema::create('campus_course_teachers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('campus_courses')->cascadeOnDelete();
                $table->foreignId('teacher_id')->constrained('campus_teachers')->cascadeOnDelete();
                $table->string('role', 20);
                $table->string('match_method', 40);
                $table->timestamps();
                $table->unique(['course_id', 'teacher_id', 'role'], 'campus_course_teacher_role_unique');
            });
        }
    }

    public function down(): void
    {
        // Imported years/sections cannot safely fit the previous unique constraint.
        throw new RuntimeException('Official catalog migration is forward-only. Restore a database backup to undo imported data.');
    }
};

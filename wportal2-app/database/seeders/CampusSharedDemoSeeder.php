<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

class CampusSharedDemoSeeder extends Seeder
{
    public function run(): void
    {
        $path     = database_path('seeders/data/campus-demo-snapshot.json');
        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new RuntimeException('Demo snapshot is missing.');
        }
        $data     = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        DB::transaction(function () use ($data) {
            foreach ($data['clubs'] as $club) {
                $this->save('campus_club_catalog', ['university' => $club['university'], 'name' => $club['name']], $club);
            }
            $teachers = $courses = $students = [];
            foreach ($data['teachers'] as $teacher) {
                $teachers[$teacher['source_key']] = $this->save('campus_teachers', ['university' => $teacher['university'], 'source_key' => $teacher['source_key']], $teacher);
            }
            foreach ($data['courses'] as $course) {
                $courses[$course['course_code']] = $this->save('campus_courses', ['university' => $course['university'], 'academic_year' => $course['academic_year'], 'course_code' => $course['course_code']], $course);
            }
            foreach ($data['relations'] as $relation) {
                $identity = ['course_id' => $courses[$relation['course_code']], 'teacher_id' => $teachers[$relation['teacher_source_key']], 'role' => $relation['role']];
                DB::table('campus_course_teachers')->updateOrInsert($identity, ['match_method' => $relation['match_method'], 'updated_at' => now()]);
            }
            foreach ($data['students'] as $student) {
                $existing                    = DB::table('campus_students')->where('email', $student['email'])->first();
                if (! str_ends_with($student['email'], '@example.invalid') || ! str_ends_with($student['name'], '（デモ）')
                                                                           || ($existing && ($existing->name !== $student['name'] || $existing->university !== $student['university']))) {
                    throw new RuntimeException('Refusing to overwrite a non-demo student.');
                }
                if (! $existing) {
                    $student['password'] = Hash::make(Str::random(64));
                }
                $students[$student['email']] = $this->save('campus_students', ['email' => $student['email']], $student);
            }
            foreach ($data['reviews'] as $review) {
                $identity = ['student_id' => $students[$review['student_email']], 'course_id' => $courses[$review['course_code']]];
                unset($review['student_email'], $review['course_code']);
                $this->save('campus_reviews', $identity, $review);
            }
        });
        $this->command->info('Fixed demo snapshot restored: 150 students, 230 reviews, 279 courses, 92 teachers, 39 clubs.');
    }

    /** @param array<string, mixed> $identity
     * @param  array<string, mixed>  $values
     */
    private function save(string $table, array $identity, array $values): int
    {
        // Older optional review columns may already have been removed on teammates' databases.
        $values = array_intersect_key($values, array_flip(Schema::getColumnListing($table)));
        DB::table($table)->updateOrInsert($identity, $values);

        return (int) DB::table($table)->where($identity)->value('id');
    }
}

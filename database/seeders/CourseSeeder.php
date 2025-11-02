<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $courses = [
            ['course_code' => 'BSBA', 'course_name' => 'Bachelor of Science in Business Administration', 'department' => 'Business and Management'],
            ['course_code' => 'BSIT', 'course_name' => 'Bachelor of Science in Information Technology', 'department' => 'Computing and Informatics'],
            ['course_code' => 'BSA', 'course_name' => 'Bachelor of Science in Accountancy', 'department' => 'Business and Accountancy'],
            ['course_code' => 'BEED', 'course_name' => 'Bachelor of Elementary Education', 'department' => 'Education'],
            ['course_code' => 'BSN', 'course_name' => 'Bachelor of Science in Nursing', 'department' => 'Health Sciences'],
            ['course_code' => 'BSCE', 'course_name' => 'Bachelor of Science in Civil Engineering', 'department' => 'Engineering'],
            ['course_code' => 'BSCrim', 'course_name' => 'Bachelor of Science in Criminology', 'department' => 'Criminal Justice'],
            ['course_code' => 'BAComm', 'course_name' => 'Bachelor of Arts in Communication', 'department' => 'Arts and Humanities'],
            ['course_code' => 'BSHM', 'course_name' => 'Bachelor of Science in Hospitality Management', 'department' => 'Tourism and Hospitality'],
            ['course_code' => 'BSPSY', 'course_name' => 'Bachelor of Science in Psychology', 'department' => 'Social Sciences'],
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(
                ['course_code' => $course['course_code']],
                [
                    'course_name' => $course['course_name'],
                    'description' => fake()->sentence(8),
                    'department' => $course['department'],
                    'duration_years' => fake()->randomElement([2, 3, 4, 5]),
                    'is_active' => true,
                ]
            );
        }
    }
}

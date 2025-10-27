<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $courses = [
            [
                'course_code' => 'BSBA',
                'course_name' => 'Bachelor of Science in Business Administration',
                'department' => 'Business and Management',
            ],
            [
                'course_code' => 'BSIT',
                'course_name' => 'Bachelor of Science in Information Technology',
                'department' => 'Computing and Informatics',
            ],
            [
                'course_code' => 'BSA',
                'course_name' => 'Bachelor of Science in Accountancy',
                'department' => 'Business and Accountancy',
            ],
            [
                'course_code' => 'BEED',
                'course_name' => 'Bachelor of Elementary Education',
                'department' => 'Education',
            ],
            [
                'course_code' => 'BSN',
                'course_name' => 'Bachelor of Science in Nursing',
                'department' => 'Health Sciences',
            ],
            [
                'course_code' => 'BSCE',
                'course_name' => 'Bachelor of Science in Civil Engineering',
                'department' => 'Engineering',
            ],
            [
                'course_code' => 'BSCrim',
                'course_name' => 'Bachelor of Science in Criminology',
                'department' => 'Criminal Justice',
            ],
            [
                'course_code' => 'BAComm',
                'course_name' => 'Bachelor of Arts in Communication',
                'department' => 'Arts and Humanities',
            ],
            [
                'course_code' => 'BSHM',
                'course_name' => 'Bachelor of Science in Hospitality Management',
                'department' => 'Tourism and Hospitality',
            ],
            [
                'course_code' => 'BSPSY',
                'course_name' => 'Bachelor of Science in Psychology',
                'department' => 'Social Sciences',
            ],
        ];

        $course = $this->faker->randomElement($courses);

        return [
            'course_code' => $course['course_code'],
            'course_name' => $course['course_name'],
            'description' => $this->faker->sentence(8, true),
            'department' => $course['department'],
            'duration_years' => $this->faker->randomElement([2, 3, 4, 5]),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}

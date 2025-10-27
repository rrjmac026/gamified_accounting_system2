<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->student(),  // creates linked user
            'student_number' => $this->faker->unique()->numerify('S########'),
            'course_id' => Course::factory(),
            'year_level' => $this->faker->numberBetween(1, 4),
            'section_id' => null,
            'total_xp' => $this->faker->numberBetween(0, 5000),
            'current_level' => $this->faker->numberBetween(1, 50),
            'performance_rating' => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}

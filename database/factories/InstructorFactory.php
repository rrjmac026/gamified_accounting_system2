<?php

namespace Database\Factories;

use App\Models\Instructor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class InstructorFactory extends Factory
{
    protected $model = Instructor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->instructor(),
            'employee_id' => $this->faker->unique()->numberBetween(1000, 9999),
            'department' => $this->faker->randomElement(['Mathematics', 'Science', 'Arts', 'History', 'Physical Education']),
            'specialization' => $this->faker->randomElement(['Algebra', 'Biology', 'Painting', 'World History', 'Sports Science']),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Subject;
use App\Models\Instructor;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition()
    {
        return [
            'subject_code' => strtoupper($this->faker->unique()->bothify('ACC###')),
            'subject_name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'semester' => $this->faker->randomElement(['1st', '2nd', 'Summer']),
            'academic_year' => $this->faker->year . '-' . ($this->faker->year + 1),
            'is_active' => $this->faker->boolean(90),
            'units' => $this->faker->numberBetween(2, 5),
        ];
    }
}


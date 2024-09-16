<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        
            'stu_name' => fake()->firstName(),
            'stu_lastname_m' => fake()->lastName(),
            'stu_latsname_f' => fake()->lastName(),
            'stu_dni' => fake()->numberBetween(10000000, 99999999),
            'stu_code' => fake(),
            'stu_user_id' => fake(),
        
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Adviser>
 */
class AdviserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'lastname_m' => fake()->lastName(),
            'latsname_f'=> fake()->lastName(),
            'ordic' => fake()->unique()->regexify('[A-Z]{2}[0-9]{8}[A-Z]{2}[0-9]{8}'),
            'jury_id' => fake()->randomElement(["presidente", "secretario", "vocal", ""]),
            
        ];
    }
}

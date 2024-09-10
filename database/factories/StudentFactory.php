<?php

namespace Database\Factories;
use Faker\Factory as Faker;
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
        $faker = Faker::create('es_ES');
        return [
            'name' => fake()->name(),
            'lastname_m' => fake()->lastName(),
            'latsname_f'=> fake()->lastName(),
            'dni' => fake()->unique()->regexify('[0-9]{8}'),
            'code' => fake()->unique()->numerify(fake()->numberBetween(2000, 2024) . str_pad(fake()->numberBetween(1, 12), 2, '0', STR_PAD_LEFT) . str_pad(fake()->numberBetween(1, 31), 2, '0', STR_PAD_LEFT) . fake()->numberBetween(10, 99)),
            'investigation_title' =>fake()->unique()->sentence(6, true),
            'adviser_id' => fake()-> name(),
        ];
    }
}

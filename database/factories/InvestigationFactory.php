<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Investigation>
 */
class InvestigationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tipe_id' => fake()->md5(),
            'archive' => fake()->mimeType(),
            'student_id' => fake()->md5(),
            'jury_id' => array(fake()->md5(),fake()->md5(),fake()->md5(),),
        ];
    }
}

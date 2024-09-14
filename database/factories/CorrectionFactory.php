<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Correction>
 */
class CorrectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'investigation_id' => fake()-> md5(),
            'adviser_id' => fake()-> md5(),
            'state_id' => fake()-> md5(),
            'archive' => fake()->mimeType(),
            'comment' => fake()->sentence(6, true),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => fake()-> md5(),
            'name' => fake()->unique()->randomElement([
                "Carta de aceptación, Resolución de designación de asesor",
                "Informe de conformidad del proyecto de tesis",
                "Informe de conformidad de proyecto de Tesis por los jurados",
                "Resolución de aprobacion de proyecto de tesis",
                "Informe de conformidad del informe final por el asesor",
                "Resolución de designación de jurados",
                "Informe de comformidad de Informe Final por los jurados",
                "Resolución de aprobación del informe final",
                ]),
            'tipe' => fake()->fileExtension(),
            'archive' => fake()->mimeType(),
            'state_id' => fake()-> md5(),

        ];
    }
}

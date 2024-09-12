<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Procedure>
 */
class ProcedureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Designación de asesor',
                'Conformidad del proyecto de tesis por el asesor',
                'Designación de jurados',
                'Conformidad del proyecto de tesis por los jurados',
                'Aprobación del proyecto de tesis por la facultad',
                'Conformidad de informe final por el asesor',
                'Designación de jurados para revisión de informe final',
                'Conformidad del informe final por los jurados',
                'Aprobación del informe final por la facultad',
                'Declaración como apto para sustentar',
                'Solicitud de fecha y hora de sustentación',
                'Sustentación',
                'Tramitar el grado o titulo',
                ]),
            'expediente' => fake()->regexify('[0-9]{6}-[0-9]{10}'),
            'student_id' => fake()-> md5(),
            'state_id' => fake()->md5(),
            'secre_school_id' => fake()->md5(),
            'secre_pa_id' => fake()->md5(),
        ];
    }
}

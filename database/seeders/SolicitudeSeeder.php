<?php

namespace Database\Seeders;

use App\Models\Adviser;
use App\Models\Solicitude;
use App\Models\Student;
use App\Models\Paisi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SolicitudeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::select('_id')->get();
        $advisers = Adviser::select('_id')->get();
        $paisis = Paisi::select('_id')->get();
        $solicitudes = [
            [
                'sol_title_inve' => "Desarrollo de un Sistema de Gestión Académica con Laravel",
                'sol_type_inve' => 'cientifica',
                'adviser_id' => $advisers[1]->_id,
                'paisi_id' => $paisis[0]->_id,
                'sol_status' => 'aceptado',
                'sol_num' => '008',
                'student_id' => $students[0]->_id,
            ],
            [
                'sol_title_inve' => 'Impacto del Uso de IoT en Sistemas Acuapónicos',
                'sol_type_inve' => 'cientifica',
                'adviser_id' => $advisers[1]->_id,
                'paisi_id' => $paisis[0]->_id,
                'sol_status' => 'rechazado',
                'sol_num' => '015',
                'student_id' => $students[1]->_id,
            ],
            [
                'sol_title_inve' => 'Análisis de Big Data en el Sector de la Salud',
                'sol_type_inve' => 'cientifica',
                'adviser_id' => $advisers[2]->_id,
                'paisi_id' => $paisis[0]->_id,
                'sol_status' => 'aceptado',
                'sol_num' => '002',
                'student_id' => $students[2]->_id,
            ],
            [
                'sol_title_inve' => "Optimización de Energía en Sistemas de Smart Grids",
                'sol_type_inve' => 'tecnologica',
                'adviser_id' => $advisers[3]->_id,
                'paisi_id' => $paisis[0]->_id,
                'sol_status' => 'aceptado',
                'sol_num' => '019',
                'student_id' => $students[4]->_id,
            ],
            [
                'sol_title_inve' => "Aplicación de la Realidad Aumentada en la Educación Universitaria",
                'sol_type_inve' => 'tecnologica',
                'adviser_id' => $advisers[3]->_id,
                'paisi_id' => $paisis[0]->_id,
                'sol_status' => 'aceptado',
                'sol_num' => '019',
                'student_id' => $students[6]->_id,
            ],
            [
                'sol_title_inve' => null,
                'adviser_id' => null,
                'paisi_id' => $paisis[0]->_id,
                'sol_status' => 'en progreso',
                'sol_num' => '099',
                'student_id' => $students[5]->_id,
            ],
        ];

        foreach ($solicitudes as $soli) {
            Solicitude::create($soli);
        }
    }
}

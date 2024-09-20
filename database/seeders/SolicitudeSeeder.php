<?php

namespace Database\Seeders;

use App\Models\Adviser;
use App\Models\Solicitude;
use App\Models\Student;
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
        $solicitudes = [
            [
                'sol_title_inve' => null,
                'adviser_id' => null,
                'sol_status' => 'pendiente',
                'student_id' => $students[0]->_id,
            ],
            [
                'sol_title_inve' => 'Tesis 1',
                'adviser_id' => $advisers[1]->_id,
                'sol_status' => 'rechazado',
                'student_id' => $students[1]->_id,
            ],
            [
                'sol_title_inve' => 'Tesis 2',
                'adviser_id' => $advisers[2]->_id,
                'sol_status' => 'aceptado',
                'student_id' => $students[2]->_id,
            ],
            [
                'sol_title_inve' => null,
                'adviser_id' => null,
                'sol_status' => 'pendiente',
                'student_id' => $students[4]->_id,
            ],
            [
                'sol_title_inve' => null,
                'adviser_id' => null,
                'sol_status' => 'pendiente',
                'student_id' => $students[5]->_id,
            ],
        ];

        foreach ($solicitudes as $soli) {
            Solicitude::create($soli);
        }
    }
}

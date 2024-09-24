<?php

namespace Database\Seeders;

use App\Models\DocOf;
use App\Models\Solicitude;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocOfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $solicitudes = Solicitude::select('_id')->get();
        $docof = [
            [
                'of_name' => 'Solicitud de resolución de designación de asesor',
                'solicitude_id' => $solicitudes[0]->_id,
                'of_num_of' => '0000011',
                'of_status' => 'pendiente',
            ],
            [
                'of_name' => 'Solicitud de resolución de designación de asesor',
                'solicitude_id' => $solicitudes[1]->_id,
                'of_num_of' => '0000022',
                'of_status' => 'tramitado',
            ],
            [
                'of_name' => 'Solicitud de resolución de designación de asesor',
                'solicitude_id' => $solicitudes[2]->_id,
                'of_num_of' => '0000033',
                'of_status' => 'observado',
            ],
            
        ];

        foreach ($docof as $office) {
            DocOf::create($office);
        }
    }
}

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
                'of_num_of' => '111',
                'of_num_exp' => '498351-0000003820',
                'of_status' => 'tramitado',
            ],
            [
                'of_name' => 'Solicitud de resolución de designación de asesor',
                'solicitude_id' => $solicitudes[2]->_id,
                'of_num_of' => '222',
                'of_num_exp' => '498351-0000003821',
                'of_status' => 'tramitado',
            ],
            [
                'of_name' => 'Solicitud de resolución de designación de asesor',
                'solicitude_id' => $solicitudes[3]->_id,
                'of_num_of' => '333',
                'of_num_exp' => '498351-0000003822',
                'of_status' => 'observado',
            ],
            [
                'of_name' => 'Solicitud de resolución de designación de asesor',
                'solicitude_id' => $solicitudes[4]->_id,
                'of_num_of' => '444',
                'of_num_exp' => '498351-0000003823',
                'of_status' => 'observado',
            ],
            
        ];

        foreach ($docof as $office) {
            DocOf::create($office);
        }
    }
}

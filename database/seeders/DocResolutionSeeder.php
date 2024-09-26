<?php

namespace Database\Seeders;

use App\Models\DocOf;
use App\Models\DocResolution;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocResolutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $office = DocOf::select('_id')->get();
        $docres = [
            [
                'docof_id' => $office[0]->_id,
                'docres_name' => 'Resolución de designación de asesor',
                'docres_num_res' => '1111',
                'docres_status' => 'tramitado',
            ],
            [
                'docof_id' => $office[1]->_id,
                'docres_name' => 'Resolución de designación de asesor',
                'docres_num_res' => '2222',
                'docres_status' => 'pendiente',
            ],
            [
                'docof_id' => $office[2]->_id,
                'docres_name' => 'Resolución de designación de asesor',
                'docres_num_res' => '3333',
                'docres_status' => 'observado',
            ],
            
        ];

        foreach ($docres as $resolution) {
            DocResolution::create($resolution);
        }
    }
}

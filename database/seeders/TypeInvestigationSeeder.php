<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeInvestigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data['name'] = "Proyecto de tesis";
        \App\Models\TypeInvestigation::create($data);

        $data['name'] = "Informe final";
        \App\Models\TypeInvestigation::create($data);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipeInvestigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data['name'] = "Proyecto de tesis";
        \App\Models\TipeInvestigation::create($data);

        $data['name'] = "Informe final";
        \App\Models\TipeInvestigation::create($data);
    }
}

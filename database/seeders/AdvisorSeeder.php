<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Advisor;

class AdvisorSeeder extends Seeder
{
    public function run()
    {
        Advisor::create([
            'name' => 'Dr. Nombre Advisor 1',
            'email' => 'advisor1@universidad.edu',
            'available' => true,
            'specialization' => 'Ingeniería de Software',
            'students_assigned' => []
        ]);

        Advisor::create([
            'name' => 'Dr. Nombre Advisor 2',
            'email' => 'advisor2@universidad.edu',
            'available' => true,
            'specialization' => 'Redes',
            'students_assigned' => []
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JurySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data['position'] = "presidente";
        \App\Models\Jury::create($data);

        $data['position'] = "secretario";
        \App\Models\Jury::create($data);

        $data['position'] = "vocal";
        \App\Models\Jury::create($data);
    }
}

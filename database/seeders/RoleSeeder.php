<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data['name'] = "student";
        \App\Models\Role::create($data);

        $data['name'] = "adviser";
        \App\Models\Role::create($data);

        $data['name'] = "sec_fac";
        \App\Models\Role::create($data);

        $data['name'] = "sec_pa";
        \App\Models\Role::create($data);
    }
}

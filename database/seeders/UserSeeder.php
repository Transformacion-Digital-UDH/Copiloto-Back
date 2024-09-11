<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data['name'] = "admin";
        $data['email'] = "admin@udh.edu.pe";
        $data['password'] = bcrypt(123456);
        $data['role_id'] = "admin";
        \App\Models\User::create($data);

        $data['name'] = "Estudiante";
        $data['email'] = "estudiante@udh.edu.pe";
        $data['password'] = bcrypt(123456);
        $data['school'] = "ingeniería";
        $data['program'] = "ingeniería de sistemas e informática";
        $data['role_id'] = "student";
        \App\Models\User::create($data);

        $data['name'] = "Asesor";
        $data['email'] = "asesor@udh.edu.pe";
        $data['password'] = bcrypt(123456);
        $data['school'] = "ingeniería";
        $data['program'] = "ingeniería de sistemas e informática";
        $data['role_id'] = "adviser";
        \App\Models\User::create($data);

        $data['name'] = "PAISI";
        $data['email'] = "paisi@udh.edu.pe";
        $data['password'] = bcrypt(123456);
        $data['school'] = "ingeniería";
        $data['program'] = "ingeniería de sistemas e informática";
        $data['role_id'] = "sec_pa";
        \App\Models\User::create($data);


        $data['name'] = "FACULTAD";
        $data['email'] = "facultad@udh.edu.pe";
        $data['password'] = bcrypt(123456);
        $data['school'] = "ingeniería";
        $data['program'] = "ingeniería de sistemas e informática";
        $data['role_id'] = "sec_fac";
        \App\Models\User::create($data);
    }
}

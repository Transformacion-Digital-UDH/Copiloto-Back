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
        $data['email'] = "admin@gmail.com";
        $data['role'] = "admin";
        $data['password'] = bcrypt(123456);
        \App\Models\User::create($data);

        $data['name'] = "Estudiante";
        $data['email'] = "estudiante@gmail.com";
        $data['role'] = "estudiante";
        $data['password'] = bcrypt(123456);
        \App\Models\User::create($data);

        $data['name'] = "Facultad";
        $data['email'] = "facultad@gmail.com";
        $data['role'] = "facultad";
        $data['password'] = bcrypt(123456);
        \App\Models\User::create($data);

        $data['name'] = "PAISI";
        $data['email'] = "paisi@gmail.com";
        $data['role'] = "paisi";
        $data['password'] = bcrypt(123456);
        \App\Models\User::create($data);

        $data['name'] = "Asesor";
        $data['email'] = "asesor@gmail.com";
        $data['role'] = "asesor";
        $data['password'] = bcrypt(123456);
        \App\Models\User::create($data);

        $data['name'] = "Jurado";
        $data['email'] = "jurado@gmail.com";
        $data['role'] = "jurado";
        $data['password'] = bcrypt(123456);
        \App\Models\User::create($data);

        $data['name'] = "Coordinador";
        $data['email'] = "coordinador@gmail.com";
        $data['role'] = "coordinador";
        $data['password'] = bcrypt(123456);
        \App\Models\User::create($data);
    }
}

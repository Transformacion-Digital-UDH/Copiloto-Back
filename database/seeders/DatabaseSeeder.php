<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data['name'] = "Jonathan";
        $data['email'] = "jonathan123@gmail.com";
        $data['role'] = "admin";
        $data['password'] = bcrypt(123456);
        \App\Models\User::create($data);

        $data['name'] = "Estudiante";
        $data['email'] = "estudiante@gmail.com";
        $data['role'] = "estudiante";
        $data['password'] = bcrypt(123456);
        $estudiante = \App\Models\User::create($data);
   

        $data['name'] = "PAISI";
        $data['email'] = "paisi@gmail.com";
        $data['role'] = "paisi";
        $data['password'] = bcrypt(123456);
        $coordinador = \App\Models\User::create($data);
     

        $data['name'] = "Coordinador";
        $data['email'] = "coordinador@gmail.com";
        $data['role'] = "coordinador";
        $data['password'] = bcrypt(123456);
        $coordinador = \App\Models\User::create($data);
 

    }
}

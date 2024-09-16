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
        $data['faculty'] = "ingeniería";
        $data['program'] = "ingeniería de sistemas e informática";
        $data['role_id'] = "admin";
        \App\Models\User::create($data);

        $data['name'] = "Estudiante";
        $data['email'] = "estudiante@udh.edu.pe";
        $data['password'] = bcrypt(123456);
        $data['program'] = "ingeniería de sistemas e informática";
        $data['role_id'] = "estudiante";
        \App\Models\User::create($data);

        $data['name'] = "Asesor";
        $data['email'] = "asesor@udh.edu.pe";
        $data['password'] = bcrypt(123456);
        $data['program'] = "ingeniería de sistemas e informática";
        $data['role_id'] = "asesor";
        \App\Models\User::create($data);

        $data['name'] = "PAISI";
        $data['email'] = "paisi@udh.edu.pe";
        $data['password'] = bcrypt(123456);
        $data['program'] = "ingeniería de sistemas e informática";
        $data['role_id'] = "secretaria_pa";
        \App\Models\User::create($data);


        $data['name'] = "FACULTAD";
        $data['email'] = "facultad@udh.edu.pe";
        $data['password'] = bcrypt(123456);
        $data['program'] = "ingeniería de sistemas e informática";
        $data['role_id'] = "secretaria_fa";
        \App\Models\User::create($data);

        $data['name'] = "KEVIN ARNOLD FLORES PACHECO";
        $data['email'] = "2018110451@udh.edu.pe";
        $data['password'] = bcrypt(123456);
        $data['faculty'] = "ingeniería";
        $data['program'] = "ingeniería de sistemas e informática";
        $data['role_id'] = "estudiante";
        \App\Models\User::create($data);

        $data['name'] = "RENZO PAOLO LUCIANO ESTELA";
        $data['email'] = "2018110461@udh.edu.pe";
        $data['password'] = bcrypt(123456);
        $data['faculty'] = "ingeniería";
        $data['program'] = "ingeniería de sistemas e informática";
        $data['role_id'] = "estudiante";
        \App\Models\User::create($data);

        $data['name'] = "JOEL JOSUE INQUIEL CALDERON";
        $data['email'] = "2018110397@udh.edu.pe";
        $data['password'] = bcrypt(123456);
        $data['faculty'] = "ingeniería";
        $data['program'] = "ingeniería de sistemas e informática";
        $data['role_id'] = "estudiante";
        \App\Models\User::create($data);

        $data['name'] = "RENZO ANDRE PANDURO MOSCOSO";
        $data['email'] = "2020160035@udh.edu.pe";
        $data['password'] = bcrypt(123456);
        $data['faculty'] = "ingeniería";
        $data['program'] = "ingeniería de sistemas e informática";
        $data['role_id'] = "estudiante";
        \App\Models\User::create($data);

        $data['name'] = "MARYCIELO MARTEL";
        $data['email'] = "2020210311@udh.edu.pe";
        $data['password'] = bcrypt(123456);
        $data['faculty'] = "ingeniería";
        $data['program'] = "ingeniería de sistemas e informática";
        $data['role_id'] = "estudiante";
        \App\Models\User::create($data);

    }
}

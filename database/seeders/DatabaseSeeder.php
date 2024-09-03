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
        // users random
        //\App\Models\User::factory(4)->create();
        
        // user role asesor
        \App\Models\User::factory()->create([
            'name' => 'Asesor',
            'email' => 'asesor@gmail.com',
            'role' => 'asesor',
        ]);
        // user role estudiante
        \App\Models\User::factory()->create([
            'name' => 'Estudiante',
            'email' => 'estudiante@gmail.com',
            'role' => 'estudiante',
        ]);
        // user role spaisi
        \App\Models\User::factory()->create([
            'name' => 'secretaria paisi',
            'email' => 'spaisi@gmail.com',
            'role' => 'spaisi',
        ]);
        // user role sfac
        \App\Models\User::factory()->create([
            'name' => 'secretaria facultad',
            'email' => 'sfac@gmail.com',
            'role' => 'sfac',
        ]);
    }
}

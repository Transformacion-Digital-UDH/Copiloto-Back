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
        $this->call(UserSeeder::class);
        $this->call(AdminSeeder::class);                
        //$this->call(SolicitudeSeeder::class);                
        //$this->call(DocOfSeeder::class);                
        //$this->call(DocResolutionSeeder::class);                
    }
}

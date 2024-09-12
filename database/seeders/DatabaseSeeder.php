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
        $this->call(RoleSeeder::class);
        $this->call(JurySeeder::class);
        $this->call(AdviserSeeder::class);
        $this->call(StudentSeeder::class);
        $this->call(TipeInvestigationSeeder::class);
        $this->call(InvestigationSeeder::class);
        $this->call(StatusSeeder::class);
        $this->call(CorrectionSeeder::class);
        $this->call(DocumentSeeder::class);
        $this->call(ApplicationSeeder::class);
        $this->call(RequirementSeeder::class);
        $this->call(ProcedureSeeder::class);
        
    }
}

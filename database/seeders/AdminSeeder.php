<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos
        $permissions = [
            'create-roles',
            'view-roles',
            'assign-permissions',
            'create-permissions',
            'view-permissions',
            'view-role-permissions'

        ];

        foreach ($permissions as $permissionName) {
            Permission::updateOrCreate([
                'name' => $permissionName
            ], [
                'description' => 'Permission to ' . $permissionName
            ]);
        }

        // Obtener IDs de permisos
        $permissionIds = Permission::whereIn('name', $permissions)->pluck('_id')->toArray(); // Asegurar que sea un array

        // Crear rol de admin con IDs de permisos
        $adminRole = Role::create([
            'name' => 'admin',
            'permission_ids' => $permissionIds
        ]);

        // Crear el primer usuario con el rol de "admin"
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@udh.edu.pe',
            'password' => bcrypt('password123'), // Hashear la contraseña
            'role_id' => $adminRole->_id, // Asignar el ID del rol de admin
        ]);
    }
}


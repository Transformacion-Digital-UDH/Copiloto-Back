<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // Obtener todos los roles con sus permisos
    public function getAllRoles()
    {
        // Obtener todos los roles y los permisos relacionados
        $roles = Role::with('permissions')->get();
        return response()->json($roles);
    }

    // Obtener los permisos de un rol específico
    public function getRolePermissions($roleId)
    {
        // Buscar el rol por ID
        $role = Role::find($roleId);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        // Obtener los permisos asociados a través de los permission_ids
        $permissions = Permission::whereIn('_id', $role->permission_ids)->get();

        return response()->json($permissions);
    }

    // Crear un rol nuevo con permisos
    public function createRole(Request $request)
    {
        // Validar la entrada
        $validatedData = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array', // Se espera un array de nombres de permisos
        ]);

        // Obtener los IDs de permisos basados en los nombres proporcionados
        $permissionIds = Permission::whereIn('name', $request->input('permissions', []))->pluck('_id')->toArray();

        // Crear el rol con los permission_ids
        $role = Role::create([
            'name' => $validatedData['name'],
            'permission_ids' => $permissionIds
        ]);

        return response()->json(['message' => 'Role created successfully', 'role' => $role]);
    }

    // Asignar permisos a un rol existente
    public function assignPermissions(Request $request, $roleId)
    {
        // Validar la entrada
        $validatedData = $request->validate([
            'permissions' => 'array', // Se espera un array de nombres de permisos
        ]);

        // Buscar el rol por ID
        $role = Role::find($roleId);
        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        // Obtener los IDs de permisos basados en los nombres proporcionados
        $newPermissionIds = Permission::whereIn('name', $request->input('permissions', []))->pluck('_id')->toArray();

        // Actualizar los permission_ids del rol (combinar con los existentes)
        $role->permission_ids = array_unique(array_merge($role->permission_ids ?? [], $newPermissionIds));
        $role->save();

        return response()->json(['message' => 'Permissions assigned successfully', 'role' => $role]);
    }
}

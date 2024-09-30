<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    // Obtener todos los permisos
    public function getAllPermissions()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    // Crear nuevos permisos
    public function createPermission(Request $request)
    {
        $permissionsData = $request->all();

        // Validar si la solicitud contiene un solo permiso o múltiples permisos
        if (isset($permissionsData['name']) && !isset($permissionsData[0])) {
            // Validación para un solo permiso
            $validator = Validator::make($permissionsData, [
                'name' => 'required|string|unique:permissions,name', // El nombre debe ser único
                'description' => 'nullable|string|max:255',
            ]);
        } else {
            // Validación para múltiples permisos
            $validator = Validator::make($permissionsData, [
                '*.name' => 'required|string|distinct', // El nombre debe ser único para cada permiso
                '*.description' => 'nullable|string|max:255',
            ]);
        }

        // Si la validación falla
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $permissions = [];

            // Crear permisos
            if (isset($permissionsData['name']) && !isset($permissionsData[0])) {
                // Crear un solo permiso
                $permissions[] = Permission::create([
                    'name' => $permissionsData['name'],
                    'description' => $permissionsData['description'] ?? ''
                ]);
            } else {
                // Crear múltiples permisos
                foreach ($permissionsData as $permissionData) {
                    $permissions[] = Permission::create([
                        'name' => $permissionData['name'],
                        'description' => $permissionData['description'] ?? ''
                    ]);
                }
            }

            return response()->json([
                'message' => 'Permissions created successfully',
                'permissions' => $permissions
            ], 201);
        } catch (\Exception $e) {
            // Manejar cualquier excepción
            return response()->json([
                'message' => 'Failed to create permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

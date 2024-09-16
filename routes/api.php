<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;

// rutas para autenticacion
Route::post('login', [AuthController::class, 'login']); // inicio de sesión
Route::post('login/google', [AuthController::class, 'loginGoogle']); // inicio de sesión
Route::post('register', [AuthController::class, 'register']); // registrar usuario
Route::post('register/google', [AuthController::class, 'registerGoogle']); // inicio de sesión

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']); //cierre de sesión
    Route::get('/me', [AuthController::class, 'me']);
});

Route::apiResource('users', \App\Http\Controllers\UserController::class);

//RUTAS PARA ROLES Y PERMISOS
Route::middleware(['auth:sanctum'])->group(function () {
    // Rutas para roles
    Route::get('/roles', [RoleController::class, 'getAllRoles'])->middleware('permission:view-roles'); //Listar todos los Roles
    Route::get('/roles/{roleId}/permissions', [RoleController::class, 'getRolePermissions'])->middleware('permission:view-role-permissions'); //Listar todos los permisos de un rol
    Route::post('/roles', [RoleController::class, 'createRole'])->middleware('permission:create-roles'); //Crear rol
    Route::post('/roles/{roleId}/permissions', [RoleController::class, 'assignPermissions'])->middleware('permission:assign-permissions'); //Asignar permisos a un rol 
    // Rutas para permisos
    Route::get('/permissions', [PermissionController::class, 'getAllPermissions'])->middleware('permission:view-permissions'); //Listar todos los permisos
    Route::post('/permissions', [PermissionController::class, 'createPermission'])->middleware('permission:create-permissions'); //Crear permisos (Pueden ser muchos o un permiso)
});





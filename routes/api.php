<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
// rutas para autenticacion

Route::post('login', [AuthController::class, 'login']); // inicio de sesión
Route::post('register', [AuthController::class, 'register']); // registrar usuario

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']); //cierre de sesión
    Route::get('/me', [AuthController::class, 'me']);
});

Route::apiResource('users', \App\Http\Controllers\UserController::class);


//Students:
Route::middleware(['auth:sanctum', 'role:student'])->post('/select-advisor', [StudentController::class, 'selectAdvisor']);
Route::middleware(['auth:sanctum', 'role:student'])->post('/register-thesis', [StudentController::class, 'registerThesis']);

Route::middleware(['auth:sanctum'])->group(function () {
    // Rutas para roles
    Route::get('/roles', [RoleController::class, 'getAllRoles'])->middleware('permission:view-roles');
    Route::get('/roles/{roleId}/permissions', [RoleController::class, 'getRolePermissions'])->middleware('permission:view-role-permissions');
    Route::post('/roles', [RoleController::class, 'createRole'])->middleware('permission:create-roles');
    Route::post('/roles/{roleId}/permissions', [RoleController::class, 'assignPermissions'])->middleware('permission:assign-permissions');

    // Rutas para permisos
    Route::get('/permissions', [PermissionController::class, 'getAllPermissions'])->middleware('permission:view-permissions');
    Route::post('/permissions', [PermissionController::class, 'createPermission'])->middleware('permission:create-permissions');
});
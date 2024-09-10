<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas para AUTENTIFICACIÓN

Route::post('login', [AuthController::class, 'login']); // inicio de sesión
Route::post('register', [AuthController::class, 'register']); // registrar usuario

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']); //cierre de sesión del token
    Route::get('/me', [AuthController::class, 'me']); // obtener datos del usuario autenticado
});

// Rutas de recursos de USUARIO

Route::apiResource('users', \App\Http\Controllers\UserController::class); //Ver users

//Rutas para ROLES y PERMISOS

Route::apiResource('roles', \App\Http\Controllers\RoleController::class); //Ver roles
Route::apiResource('permissions', \App\Http\Controllers\PermissionController::class); //Ver permisos
Route::apiResource('permissionRoles', \App\Http\Controllers\PermissionRoleController::class); //Ver permisos con roles

//Rutas para ESTUDIANTES

//Rutas para ADVISER (ASESORES Y JURADOS)

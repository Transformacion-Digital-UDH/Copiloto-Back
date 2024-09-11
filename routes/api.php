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
Route::apiResource('permissionroles', \App\Http\Controllers\PermissionRoleController::class); //Ver permisos con roles
Route::apiResource('juries', \App\Http\Controllers\JuryController::class); //Ver permisos con roles

//Rutas para ESTUDIANTES
Route::apiResource('students', \App\Http\Controllers\StudentController::class); //Ver estudiantes

//Rutas para ADVISER (ASESORES Y JURADOS)
Route::apiResource('advisers', \App\Http\Controllers\AdviserController::class); //Ver revisores (asesores)

//Rutas para INVESTIGACIONES(TESIS, E INFORME FINAL)
Route::apiResource('investigations', \App\Http\Controllers\InvestigationController::class); //Ver revisores (asesores)
Route::apiResource('tipeinvestigations', \App\Http\Controllers\TipeInvestigationController::class); //Ver revisores (asesores)

//Rutas para Status
Route::apiResource('status', \App\Http\Controllers\StatusController::class); //Ver revisores (asesores)

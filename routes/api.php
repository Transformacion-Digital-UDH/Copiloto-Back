<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
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


//Students:
Route::middleware(['auth:sanctum', 'role:student'])->post('/select-advisor', [StudentController::class, 'selectAdvisor']);
Route::middleware(['auth:sanctum', 'role:student'])->post('/register-thesis', [StudentController::class, 'registerThesis']);

//RUTAS PARA ROLES Y PERMISOS
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


//Rutas para ESTUDIANTES
Route::apiResource('students', \App\Http\Controllers\StudentController::class); //Ver estudiantes
Route::apiResource('documents', \App\Http\Controllers\DocumentController::class); //Ver documentos
Route::apiResource('applications', \App\Http\Controllers\ApplicationController::class); //Ver estudiantes

//Rutas para ADVISER (ASESORES Y JURADOS)
Route::apiResource('advisers', \App\Http\Controllers\AdviserController::class); //Ver revisores (asesores)

//Rutas para INVESTIGACIONES(TESIS, E INFORME FINAL)
Route::apiResource('investigations', \App\Http\Controllers\InvestigationController::class); //Ver investigaciones 
Route::apiResource('typeinvestigations', \App\Http\Controllers\TypeInvestigationController::class); //Ver tipo de investigacion 
Route::apiResource('corrections', \App\Http\Controllers\CorrectionController::class); //Ver correcciones 

//Rutas para Status
Route::apiResource('status', \App\Http\Controllers\StatusController::class); //Ver estados 

//Rutas para trámites
Route::apiResource('requirements', \App\Http\Controllers\RequirementController::class); //Ver requisitos 
Route::apiResource('procedures', \App\Http\Controllers\ProcedureController::class); //Ver procesos


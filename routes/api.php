<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
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


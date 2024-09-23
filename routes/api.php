<?php

use App\Http\Controllers\AdviserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\SolicitudeController;
use App\Http\Controllers\GoogleDocumentController;
use App\Http\Controllers\StudentController;

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
    // Rutas de asesor
    Route::get('/get-select', [AdviserController::class, 'getToSelect']); // Obtener todos los asesores para seleccionar
});

//RUTAS PARA SOLICITUDES
Route::middleware(['auth:sanctum'])->group(function () {
    // Ruta para crear una nueva solicitud
    Route::post('/solicitudes-store', [SolicitudeController::class, 'store']);
    // Actualizar título de tesis y asesor
    Route::put('/solicitudes/{id}', [SolicitudeController::class, 'updateSolicitude'])->middleware('permission:update-solicitude');
    // Ruta para actualizar el estado de una solicitud
    Route::put('/solicitudes/{id}/status', [SolicitudeController::class, 'updateStatus']);
    // Ruta para ver solicitudes aceptadas para -> PAISI
    Route::get('/paisi/getSolicitude', [SolicitudeController::class, 'getSolicitudeForPaisi']); 
    // Ruta para listar solicitudes ordenando por estado (PENDIENTE, ACEPTADO, RECHAZADO) por id de asesor
    Route::get('/adviser/getSolicitude/{adviser_id}', [SolicitudeController::class, 'getSolicitudeToAdviser']); 
    
});

Route::middleware(['auth:sanctum'])->group(function () {   
    // Ruta para ver solicitudes, oficio y resoluciones de estudiante por id
    Route::get('/student/getInfo/{student_id}', [StudentController::class, 'getInfoStudentById']); 

});

//RUTA PARA DOCUMENTO GOOGLE
Route::post('/create-document', [GoogleDocumentController::class, 'createDocument']); //Crear gocumento de google docs (Tesis)
Route::get('document-link/{solicitudeId}', [GoogleDocumentController::class, 'getDocumentLink']); //Obtener link del documento de google docs (Tesis)

//Ruta para generar PDF de carta de aceptacion
Route::get('/letter-accepted/{id}', [SolicitudeController::class, 'verPDF'])->name('letter-accepted');

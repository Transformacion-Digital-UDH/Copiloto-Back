<?php

use App\Http\Controllers\AdviserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\SolicitudeController;
use App\Http\Controllers\DocOfController;
use App\Http\Controllers\DocResolutionController;
use App\Http\Controllers\GoogleDocumentController;
use App\Http\Controllers\HistoryReviewController;
use App\Http\Controllers\ReviewController;
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
Route::get('solicitudes', [SolicitudeController::class, 'getAll']);
Route::get('students', [StudentController::class, 'getAll']);
Route::get('advisers', [AdviserController::class, 'getAll']);

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
    
//RUTAS PARA SOLICITUDES
Route::middleware(['auth:sanctum'])->group(function () {
    // Ruta para crear una nueva solicitud ---> ESTUDIANTE
    Route::post('/solicitudes-store', [SolicitudeController::class, 'store']);
    // Actualizar título de tesis y asesor ---> ESTUDIANTE
    Route::put('/solicitudes/{id}', [SolicitudeController::class, 'updateSolicitude'])->middleware('permission:update-solicitude');
    // Ruta para actualizar el estado de una solicitud ---> ESTUDIANTE, ASESOR
    Route::patch('/solicitudes/{id}/status', [SolicitudeController::class, 'updateStatus']);
    // Ruta para ver solicitudes aceptadas para ---> PAISI
    Route::get('/paisi/getSolicitude', [SolicitudeController::class, 'getSolicitudeForPaisi']); 
});

//RUTAS PARA OFFICIOS
Route::middleware(['auth:sanctum'])->group(function () {
    //Ruta para actualizar el estado de la solicitud ---> PAISI
    Route::put('/offices/{id}/update-status-paisi', [DocOfController::class, 'updateStatusPaisi']);
});
    
//RUTAS PARA RESOLUCIONES
Route::middleware(['auth:sanctum'])->group(function () {   
    // Actualizar estado para Resolucion ---> FACULTAD
    Route::put('/resolution/{id}/status', [DocResolutionController::class, 'updateStatus']);
});

// RUTAS PARA ESTUDIANTES
Route::middleware(['auth:sanctum'])->group(function () {   
    // Ruta para ver solicitudes, oficio y resoluciones de estudiante por id
    Route::get('/student/getInfo/{student_id}', [StudentController::class, 'getInfoStudentById']); 
});

// RUTAS PARA REVISIONES
Route::middleware(['auth:sanctum'])->group(function () {  
    // Ruta para que el estudiante solicite la primera revision ---> ESTUDIANTE
    Route::post('/student/first-review/{student_id}', [ReviewController::class, 'createReview']);
    // Ruta para el actualizar estado de la revision ---> ESTUDIANTE, ASESOR
    Route::put('/student/review/{student_id}/status', [ReviewController::class, 'updateStatusReview']);
    // Ruta para ver las correcciones observadas y aprobada en orden ---> ESTUDIANTE
    Route::get('/student/get-review/{student_id}', [HistoryReviewController::class, 'viewRevisionByStudent']);   
    // Ruta para ver las correcciones pendientes ---> ASESOR
    Route::get('/adviser/get-review/{adviser_id}', [ReviewController::class, 'viewRevisionByAdviser']); 

});

Route::get('/student/get-review/{student_id}', [HistoryReviewController::class, 'viewRevisionByStudent']);   


// RUTAS PARA ASESORES
Route::middleware(['auth:sanctum'])->group(function () {   
    // Ruta para ver solicitudes, oficio y resoluciones de estudiante por id
    Route::get('/adviser/get-select', [AdviserController::class, 'getToSelect']); // Obtener todos los asesores para seleccionar
    // Ruta para listar solicitudes ordenando por estado (PENDIENTE, ACEPTADO, RECHAZADO) por id de asesor    
    Route::get('/adviser/getSolicitude/{adviser_id}', [SolicitudeController::class, 'getSolicitudeToAdviser']); 
});

//RUTA PARA DOCUMENTO GOOGLE
Route::post('/create-document', [GoogleDocumentController::class, 'createDocument']); //Crear gocumento de google docs (Tesis)
Route::get('document-link/{solicitudeId}', [GoogleDocumentController::class, 'getDocumentLink']); //Obtener link del documento de google docs (Tesis)



// Ruta para ver y generar PDF de carta de aceptacion [DA]-----> Asesor
Route::get('/view-letter/{id}', [SolicitudeController::class, 'viewPDF']);
Route::get('/download-letter/{id}', [SolicitudeController::class, 'downloadLetter']);
// Ruta para ver y generar PDF de oficio [DA]-----> PAISI
Route::get('/view-office/{id}', [DocOfController::class, 'offPDF']); 
Route::get('/download-office/{id}', [DocOfController::class, 'downloadOffice']);
// Ruta para ver y generar PDF de Resolucion [DA]-------> FACULTAD  
Route::get('/view-resolution/{id}', [DocResolutionController::class, 'resPDF']);
Route::get('/download-resolution/{id}', [DocResolutionController::class, 'downloadResolution']);
// Ruta para ver conformidad del proyecto de tesis por el asesor ---> ESTUDIANTE, ASESOR 
Route::get('/view-cpa/{id}', [HistoryReviewController::class, 'viewConfAdviser']);
Route::get('/download-cpa/{id}', [HistoryReviewController::class, 'downloadConfAdviser']);


Route::get('/faculty/getOffices', [DocOfController::class, 'getOffices']);




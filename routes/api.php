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
use App\Http\Controllers\GoogleDocumentEndController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CommentControllerDocs;

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
    //Ruta para actualizar el estado de la solicitud de designacion de asesor ---> PAISI
    Route::put('/offices/{id}/update-status-paisi', [DocOfController::class, 'updateStatusPaisi']);
    //Ruta para crear la solicitud de oficio multiple, para jurados de tesis ---> ESTUDIANTE
    Route::get('/office/solicitude-juries/{student_id}', [DocOfController::class, 'soliciteJuriesForTesis']);
    //Ruta para ver las solicitudes de designacion de jurados para la tesis ---> PAISI
    Route::get('/office/get-solicitude-juries', [DocOfController::class, 'viewSolicitudeOfJuries']);
    //Ruta para actualizar el estado del oficio de designacion de jurados --->PAISI
    Route::put('/office/djt/{docof_id}/status', [DocOfController::class, 'updateSoliciteJuriesForTesis']);
    //Ruta para crear oficio de solicitud de aprobacion de tesis por la facultad--->PAISI
    Route::get('/oficio/solicitud-aprobar-tesis/{student_id}', [DocOfController::class, 'soliciteOfficeApproveThesis']);
    //Ruta para para ver oficios en de APROBACION DE TESIS con orden --->PAISI
    Route::get('/oficio/get-aprobar-tesis', [DocOfController::class, 'getOfficeApproveThesis']);
    //Ruta para para actualizar oficios --->PAISI
    Route::put('/oficio/aprobacion-tesis/{office_id}/status', [DocOfController::class, 'updateStatusOfficeApproveThesis']);
    //Ruta Ver oficios para designacion de jurados para informe final --->Estudiante
    Route::get('/oficio/solicitud-jurados/informe', [DocOfController::class, 'getOfficeForJuriesInforme']);
    //Ruta para crear oficio de solicitud de aprobacion de informe por la facultad--->PAISI
    Route::get('/oficio/solicitud-aprobar/informe/{student_id}', [DocOfController::class, 'soliciteOfficeApproveInforme']);
    //Ruta para para ver oficios en de APROBACION DE TESIS con orden --->PAISI
    Route::get('/oficio/get-aprobar/informe', [DocOfController::class, 'getOfficeApproveInforme']);

});





//RUTAS PARA RESOLUCIONES
Route::middleware(['auth:sanctum'])->group(function () {   
    // Actualizar estado para Resolucion ---> FACULTAD
    Route::put('/resolution/{id}/status', [DocResolutionController::class, 'updateStatus']);
    //Ruta para ver las resoluciones de aprobacion de tesis ---> FACULTAD
    Route::get('/resolucion/get-aprobar-tesis', [DocResolutionController::class, 'getReslutionApproveThesis']);
    //Ruta para ver actualizar las resoluciones ---> FACULTAD
    Route::put('/resolucion/aprobacion-tesis/{docres_id}/status', [DocResolutionController::class, 'updateStatusResolutionApproveThesis']);
    //Ruta para ver las resoluciones de aprobacion de INFORME ---> FACULTAD
    Route::get('/resolucion/get-aprobar/informe', [DocResolutionController::class, 'getReslutionApproveInforme']);
    //Ruta para las resoluciones de solicitud de juados pendientes ---> FACULTAD
    Route::get('/resolucion/solicitud-jurados/informe', [DocResolutionController::class, 'getResolutionForJuriesInforme']);
    
});






// RUTAS PARA ESTUDIANTES
Route::middleware(['auth:sanctum'])->group(function () {   
    // Ruta para ver solicitudes, oficio y resoluciones de estudiante por id
    Route::get('/student/getInfo/{student_id}', [StudentController::class, 'getInfoStudentById']); 
    // Ruta para ver los jurados asignados por id de estudiante
    Route::get('/student/get-juries/{student_id}', [StudentController::class, 'viewJuriesForTesisByStudent']); 
    //Ruta para para vista de APROBACION DE TESIS ---> ESTUDIANTE
    Route::get('/estudiante/get-info-aprobar-tesis/{student_id}', [StudentController::class, 'getInfoApproveThesis']);
    //Ruta para para vista de TESIS APROBADA MAS RESOLUCIONES ---> ESTUDIANTE
    Route::get('/estudiante/get-info-tesis/aprobado/{student_id}', [StudentController::class, 'getInfoEjecucion']);
    //Ruta para ver estudiantes con tesis aprobada a espera de link para informe ---> PAISI
    Route::get('/estudiante/get-solicitud-informe', [StudentController::class, 'getStudentsInforme']);
    //Ruta para la vista del estudiante en revision de infome por el asesor --->ESTUDIANTE
    Route::get('/estudiante/info-conf-asesor/informe/{student_id}', [StudentController::class, 'getInfoConfAdviserInforme']);
    //Ruta para la vista del estudiante en solicitud de jurados para informe final --->ESTUDIANTE
    Route::get('/estudiante/info-juries/informe/{student_id}', [StudentController::class, 'infoOfficeJuriesForInforme']);
    // Crear oficio para solicitud de jurados para informe final --->Estudiante
    Route::get('/oficio/crear-solicitud-jurados/informe/{student_id}', [DocOfController::class, 'createOfficeJuriesForInforme']);
    //Ruta para para vista de APROBACION DE INFORME FINAL ---> ESTUDIANTEc
    Route::get('/estudiante/get-info-aprobar/informe/{student_id}', [StudentController::class, 'getInfoApproveInforme']);
});
    //Ruta para para vista de validación de TU COACH UDH ---> ESTUDIANTEc
    Route::get('/estudiante/get-certificado-buenas-practicas/{student_id}', [StudentController::class, 'getStateTuCoachUDH']);


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
    // Ruta para que los jurados vean las reviciones con el estado de las otra revisiones pendientes ---> ASESORES(JURADOS)
    Route::get('/adviser/get-review-jury/{adviser_id}', [ReviewController::class, 'viewReviewAsJuryForAdviser']);
    // Ruta para ver las reviciones pendientes de los jurados con informacion del estudiante ---> ESTUDIANTE
    Route::get('/review/get-review-jury/{student_id}', [ReviewController::class, 'getInfoReviewJuriesByStudent']);
    // Ruta para actualizar los estados por id de revicion ---> ESTUDIANTE - ASESOR
    Route::put('/review/{review_id}/status', [ReviewController::class, 'updateStatusReviewJuries']);
    //Ruta para crear la revision para el asesor del informe final  ---> ESTUDIANTE
    Route::get('/review/create-revision/informe/{student_id}', [ReviewController::class, 'createReviewInforme']);
    //Ruta para ver todos los estudiantes a espera de revision de informe  ---> ASESOR
    Route::get('/asesor/get-revisiones/informe/{adviser_id}', [ReviewController::class, 'getInfoConfAdviserInforme']);
});






// RUTAS PARA ASESORES
Route::middleware(['auth:sanctum'])->group(function () {   
    // Ruta para ver solicitudes, oficio y resoluciones de estudiante por id
    Route::get('/adviser/get-select', [AdviserController::class, 'getToSelect']); // Obtener todos los asesores para seleccionar
    // Ruta para listar solicitudes ordenando por estado (PENDIENTE, ACEPTADO, RECHAZADO) por id de asesor    
    Route::get('/adviser/getSolicitude/{adviser_id}', [SolicitudeController::class, 'getSolicitudeToAdviser']); 
    // Ruta para listar jurados y sus revisiones, con rol, estudiante, tiempo transcurrido en dias ---> PAISI
    Route::get('/juries/get-select', [AdviserController::class, 'getSelectJuriesTesis']); 

});

    // Ruta para listar revisiones a asesores jurados ---> ASESOR
    Route::get('/jurado/get-revision-estudiantes/informe/{adviser_id}', [AdviserController::class, 'getReviewInforme']); 
    // Ver revisiones de informe final --->Estudiante
    Route::get('/estudiante/get-revision-jurados/informe/{student_id}', [StudentController::class, 'getReviewJuriesInforme']);


//RUTA PARA DOCUMENTO GOOGLE
Route::post('/create-document', [GoogleDocumentController::class, 'createDocument']); //Crear gocumento de google docs (Tesis)
Route::get('document-link/{solicitudeId}', [GoogleDocumentController::class, 'getDocumentLink']); //Obtener link del documento de google docs (Tesis)
Route::post('/create-informe', [GoogleDocumentEndController::class, 'createInforme']);




// Ruta para ver y generar PDF de carta de aceptacion [DA]-----> Asesor
Route::get('/view-letter/{id}', [SolicitudeController::class, 'viewPDF']);
Route::get('/download-letter/{id}', [SolicitudeController::class, 'downloadLetter']);

// Ruta para ver y generar PDF de oficio [DA]-----> PAISI
Route::get('/view-office/{id}', [DocOfController::class, 'offPDF']); 
Route::get('/download-office/{id}', [DocOfController::class, 'downloadOffice']);

// Ruta para ver y generar PDF de Resolucion [DA]-------> FACULTAD  
Route::get('/view-resolution/{id}', [DocResolutionController::class, 'resPDF']);
Route::get('/download-resolution/{id}', [DocResolutionController::class, 'downloadResolution']);

// Ruta para ver conformidad del proyecto de tesis ---> ESTUDIANTE, ASESOR, JURADOS  
Route::get('/view-cpa/{review_id}', [HistoryReviewController::class, 'viewConfAdviser']);
Route::get('/download-cpa/{review_id}', [HistoryReviewController::class, 'downloadConfAdviser']);

//Ruta para ver oficio de designacion de jurados para la revision de tesis ---> ESTUDIANTE, JURADOS
Route::get('/view-odj-pt/{docof_id}', [DocOfController::class, 'viewOfficeJuriesForTesis']);
Route::get('/download-odj-pt/{docof_id}', [DocOfController::class, 'downloadOfficeJuriesForTesis']);

//Ruta para ver oficio de aprobacion de proyecto de tesis (APT) ---> ESTUDIANTE, PAISI, FACULTAD
Route::get('/oficio/ver-aprobacion-tesis/{office_id}', [DocOfController::class, 'viewOfficeApproveThesis']);
Route::get('/oficio/descargar-aprobacion-tesis/{office_id}', [DocOfController::class, 'downloadOfficeApproveThesis']);

//Ruta para ver resolucion de aprobacion de proyecto de tesis (APT) ---> ESTUDIANTE, PAISI, FACULTAD
Route::get('/resolucion/ver-aprobacion-tesis/{resolution_id}', [DocResolutionController::class, 'viewResApproveThesis']);
Route::get('/resolucion/descargar-aprobacion-tesis/{resolution_id}', [DocResolutionController::class, 'downloadResApproveThesis']);

//Ruta para ver oficio de designacion de jurados para informe final ---> ESTUDIANTE, PAISI, FACULTAD
Route::get('/office/view-oficio-jurados/informe/{docof_id}', [DocOfController::class, 'viewOfficeJuriesForInforme']);
Route::get('/office/download-oficio-jurados/informe/{docof_id}', [DocOfController::class, 'downloadOfficeJuriesForInforme']);

//Ruta para ver resolucion de designacion de jurados para informe final ---> ESTUDIANTE, PAISI, FACULTAD
Route::get('/resolucion/view-resolucion-jurados/informe/{res_id}', [DocResolutionController::class, 'viewResolutionJuriesForInforme']);
Route::get('/resolucion/download-resolucion-jurados/informe/{res_id}', [DocResolutionController::class, 'downloadResolutionJuriesForInforme']);

//Ruta para ver oficio de aprobacion de INFORME FINAL ---> ESTUDIANTE, PAISI, FACULTAD
Route::get('/oficio/ver-aprobacion/informe/{office_id}', [DocOfController::class, 'viewOfficeApproveInforme']);
Route::get('/oficio/descargar-aprobacion/informe/{office_id}}', [DocOfController::class, 'downloadOfficeApproveInforme']);

//Ruta para ver resolucion de aprobacion de INFORME FINAL (APT) ---> ESTUDIANTE, PAISI, FACULTAD
Route::get('/resolucion/ver-aprobacion/informe/{resolution_id}', [DocResolutionController::class, 'viewResApproveInforme']);
Route::get('/resolucion/download-aprobacion/informe/{resolution_id}', [DocResolutionController::class, 'downloadResApproveInforme']);



Route::get('/faculty/getOffices', [DocOfController::class, 'getOffices']);


//Ruta para extraer y guardar los comentarios
Route::post('/solicitudes/{solicitudeId}/comments/extract', [CommentControllerDocs::class, 'extractAndSaveComments']);

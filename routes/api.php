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
use App\Http\Controllers\DefenseController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\CommentExportController;

Route::get('/', function () {
    $routes = collect(Route::getRoutes())->map(function ($route) {
        return [
            'method' => implode(' | ', $route->methods()),
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'action' => $route->getActionName(),
        ];
    });
    return view('API', compact('routes'));
})->name('api');

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
Route::middleware(['auth:sanctum'])->group(function () {});

//RUTAS PARA ESTUDIANTES
Route::middleware(['auth:sanctum'])->group(function () {

    //--------->>>>> DESIGNACION DE ASESOR
    // Ruta para ver solicitudes, oficio y resoluciones de estudiante por id
    Route::get('/student/getInfo/{student_id}', [StudentController::class, 'getInfoStudentById']);
    // Ruta para crear una nueva solicitud ---> ESTUDIANTE
    Route::post('/solicitudes-store', [SolicitudeController::class, 'store']);
    // Actualizar título de tesis ---> ESTUDIANTE
    Route::put('/solicitudes/{id}', [SolicitudeController::class, 'updateSolicitude'])->middleware('permission:update-solicitude');

    //--------->>>>> CONFORMIDAD POR EL ASESOR
    // Ruta para que el estudiante solicite la primera revision ---> ESTUDIANTE
    Route::post('/student/first-review/{student_id}', [ReviewController::class, 'createReview']);
    // Ruta para ver las correcciones observadas y aprobada en orden ---> ESTUDIANTE
    Route::get('/student/get-review/{student_id}', [HistoryReviewController::class, 'viewRevisionByStudent']);

    //--------->>>>> DESIGNACION DE JURADOS - TESIS
    //Ruta para crear la solicitud de oficio multiple, para jurados de tesis ---> ESTUDIANTE
    Route::get('/office/solicitude-juries/{student_id}', [DocOfController::class, 'soliciteJuriesForTesis']);
    // Ruta para ver los jurados asignados por id de estudiante
    Route::get('/student/get-juries/{student_id}', [StudentController::class, 'viewJuriesForTesisByStudent']);
});

//RUTAS COMPARTIDAS ESTUDIANTE - ASESOR
Route::middleware(['auth:sanctum'])->group(function () {

    //--------->>>>> CONFORMIDAD POR EL ASESOR - JURADOS  
    // Ruta para el actualizar estado de la revision ---> ESTUDIANTE, ASESOR
    Route::put('/student/review/{student_id}/status', [ReviewController::class, 'updateStatusReview']);
});


//RUTAS PARA ASESORES
Route::middleware(['auth:sanctum'])->group(function () {

    //--------->>>>> DESIGNACION DE ASESOR
    // Ruta para actualizar el estado de una solicitud ---> ASESOR
    Route::patch('/solicitudes/{id}/status', [SolicitudeController::class, 'updateStatus']);

    //--------->>>>> CONFORMIDAD POR EL ASESOR DEL PROYECTO DE TESIS
    // Ruta para ver las correcciones pendientes ---> ASESOR
    Route::get('/adviser/get-review/{adviser_id}', [ReviewController::class, 'viewRevisionByAdviser']);
});


//RUTAS PARA PROGRAMA ACADEMICO
Route::middleware(['auth:sanctum'])->group(function () {

    //--------->>>>> DESIGNACION DE ASESOR

    // Ruta para ver solicitudes aceptadas para ---> PAISI
    Route::get('/paisi/getSolicitude/{pa_id}', [SolicitudeController::class, 'getSolicitudeForPaisi']);
    //Ruta para actualizar el estado de la solicitud de designacion de asesor ---> PAISI
    Route::put('/offices/{id}/update-status-paisi', [DocOfController::class, 'updateStatusPaisi']);

    //--------->>>>> DESIGNACION DE JURADOS - TESIS
    //Ruta para ver las solicitudes de designacion de jurados para la tesis ---> PAISI
    Route::get('/office/get-solicitude-juries', [DocOfController::class, 'viewSolicitudeOfJuries']);
    //Ruta para actualizar el estado del oficio de designacion de jurados --->PAISI --------> (TESIS - INFORME)
    Route::put('/office/djt/{docof_id}/status', [DocOfController::class, 'updateSoliciteJuriesForTesis']);
});


//RUTAS PARA FACULTAD
Route::middleware(['auth:sanctum'])->group(function () {

    //--------->>>>> DESIGNACION DE ASESOR

    // Ruta para ver las resoluciones de designacion de asesor ---> FACULTAD
    Route::get('/faculty/getOffices/{facultad_id}', [DocOfController::class, 'getOffices']);
    // Actualizar estado para Resolucion designacion de asesor ---> FACULTAD
    Route::put('/resolution/{id}/status', [DocResolutionController::class, 'updateStatus']);
});



//RUTAS PARA PAISI
Route::middleware(['auth:sanctum'])->group(function () {







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
    //Ruta para para ver oficios en de APROBACION DE INFORM con orden --->PAISI
    Route::get('/oficio/get-aprobar/informe', [DocOfController::class, 'getOfficeApproveInforme']);
    //Ruta para solicitar el oficio de DECLARAR APTO PARA SUSTENTAR --->ESTUDANTE
    Route::get('/oficio/declarar-apto/{student_id}', [DocOfController::class, 'soliciteOfficeDeclareApto']);
    //Ruta para para ver oficios de DECLARAR APTO PARA SUSTENTAR con orden --->PAISI
    Route::get('/oficio/get/declarar-apto', [DocOfController::class, 'getOfficeDeclareApto']);
    //Ruta para para crear oficio de DESIGNACION DE FECHA Y HORA con orden --->PAISI
    Route::get('/oficio/desigancion-fecha-hora-sustentacion/{student_id}', [DocOfController::class, 'soliciteOfficeDesignationDate']);
    //Ruta para para ver oficios de DESIGNACION DE FECHA Y HORA con orden --->PAISI
    Route::get('/oficio/get/desigancion-fecha-hora-sustentacion', [DocOfController::class, 'getOfficeDesignationDate']);
});


//RUTAS PARA RESOLUCIONES
Route::middleware(['auth:sanctum'])->group(function () {

    //Ruta para ver las resoluciones de aprobacion de tesis ---> FACULTAD
    Route::get('/resolucion/get-aprobar-tesis', [DocResolutionController::class, 'getReslutionApproveThesis']);
    //Ruta para actualizar las resoluciones ---> FACULTAD
    Route::put('/resolucion/aprobacion-tesis/{docres_id}/status', [DocResolutionController::class, 'updateStatusResolutionApproveThesis']);
    //Ruta para ver las resoluciones de aprobacion de INFORME ---> FACULTAD
    Route::get('/resolucion/get-aprobar/informe', [DocResolutionController::class, 'getReslutionApproveInforme']);
    //Ruta para las resoluciones de solicitud de juados pendientes ---> FACULTAD
    Route::get('/resolucion/solicitud-jurados/informe', [DocResolutionController::class, 'getResolutionForJuriesInforme']);
    //Ruta para ver las resoluciones DECLARAR APTO ---> FACULTAD
    Route::get('/resolucion/get/declarar-apto/informe', [DocResolutionController::class, 'getResolutionDeclareApto']);
    //Ruta para ver las resoluciones DESIGNACION DE FECHA Y HORA ---> FACULTAD
    Route::get('/resolucion/get/desigancion-fecha-hora-sustentacion', [DocResolutionController::class, 'getResolutionDesignationDate']);
});

//RUTAS PARA SUSTENTACION
Route::middleware(['auth:sanctum'])->group(function () {
    //Ruta para las resoluciones de solicitud de juados pendientes ---> ASESOR
    Route::put('/sustentacion/{sustetancion_id}/status', [DefenseController::class, 'updateStatusDefense']);
});






// RUTAS PARA ESTUDIANTES
Route::middleware(['auth:sanctum'])->group(function () {

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
    //Ruta para para vista de APROBACION DE INFORME FINAL ---> ESTUDIANTE
    Route::get('/estudiante/get-info-aprobar/informe/{student_id}', [StudentController::class, 'getInfoApproveInforme']);
    //Ruta para para vista de validación de VRI ---> ESTUDIANTE

    //Ruta para para vista de DECLARAR APTO PARA SUSTENTAR ---> ESTUDIANTE
    Route::get('/estudiante/get-info/declarar-apto/{student_id}', [StudentController::class, 'getInfoDeclareApto']);
    //Ruta para para vista de DESIGNACION DE FECHA Y HORA PARA SUSTENTACION ---> ESTUDIANTE
    Route::get('/estudiante/get-info/desigancion-fecha-hora-sustentacion/{student_id}', [StudentController::class, 'getInfoDesignationDate']);
    //Ruta para para vista de SUSTENTACION ---> ESTUDIANTE
    Route::get('/estudiante/get/resultado-sustentacion/{student_id}', [StudentController::class, 'getInfoDefenseStudent']);
});

//Ruta para vista de CONFORMIDAD POR VRI ---> ESTUDIANTE
Route::get('/estudiante/info-filtro/{student_id}', [StudentController::class, 'getInfoFilterStudent']);
//Ruta para crear revision de CONFORMIDAD POR VRI ---> ESTUDIANTE
Route::get('/vri/crear-primer-filtro/{student_id}', [FilterController::class, 'createReviewVRI']);
//Ruta para ver revisiones pendientes de PRIMER FILTRO ---> VRI
Route::get('/vri/get-primer-filtro', [FilterController::class, 'getStudentsFirstFilter']);
//Ruta para ver revisiones pendientes de SEGUNDO FILTRO ---> VRI
Route::get('/vri/get-segundo-filtro', [FilterController::class, 'getStudentsSecondFilter']);
//Ruta para ver revisiones pendientes de TERCER FILTRO ---> VRI
Route::get('/vri/get-tercer-filtro', [FilterController::class, 'getStudentsTirdFilter']);
//Ruta para ACTUALIZAR estados de FILTROS ---> VRI
Route::put('/vri/update-filter/{filter_id}/status', [FilterController::class, 'updateFilter']);
//Ruta para ver los COMENTARIOS ---> ESTUDIANTE
Route::get('/vri/coments/{student_id}', [CommentControllerDocs::class, 'getComment']);


// RUTAS PARA REVISIONES
Route::middleware(['auth:sanctum'])->group(function () {

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
    //Ruta para ver todos los estudiantes a espera de revision de sustentacion  ---> ASESOR
    Route::get('/asesor/get-revisiones/sustentacion/{adviser_id}', [ReviewController::class, 'getInfoDefenseAdviser']);
});










// RUTAS PARA ASESORES
Route::middleware(['auth:sanctum'])->group(function () {
    // Ruta para ver solicitudes, oficio y resoluciones de estudiante por id
    Route::get('/adviser/get-select', [AdviserController::class, 'getToSelect']);
    // Ruta para listar solicitudes ordenando por estado (PENDIENTE, ACEPTADO, RECHAZADO) por id de asesor    
    Route::get('/adviser/getSolicitude/{adviser_id}', [SolicitudeController::class, 'getSolicitudeToAdviser']);
    // Ruta para listar jurados y sus revisiones, con rol, estudiante, tiempo transcurrido en dias ---> PAISI
    Route::get('/juries/get-select/{oficio_id}', [AdviserController::class, 'getSelectJuriesTesis']);
    // Ruta para listar revisiones a asesores jurados ---> ASESOR
    Route::get('/jurado/get-revision-estudiantes/informe/{adviser_id}', [AdviserController::class, 'getReviewInforme']);
    // Ver revisiones de informe final --->Estudiante
    Route::get('/estudiante/get-revision-jurados/informe/{student_id}', [StudentController::class, 'getReviewJuriesInforme']);
});



//RUTA PARA DOCUMENTO GOOGLE
Route::post('/create-document', [GoogleDocumentController::class, 'createDocument']); //Crear gocumento de google docs (Tesis)
Route::get('document-link/{solicitudeId}', [GoogleDocumentController::class, 'getDocumentLink']); //Obtener link del documento de google docs (Tesis)
Route::post('/create-informe', [GoogleDocumentEndController::class, 'createInforme']);


//Ruta para extraer y guardar los comentarios
Route::post('/solicitudes/{solicitudeId}/comments/extract', [CommentControllerDocs::class, 'extractAndSaveComments']);
Route::get('/comments/{solicitudeId}/export', [CommentExportController::class, 'exportCommentsToExcel']);


require __DIR__ . '/docs.php';

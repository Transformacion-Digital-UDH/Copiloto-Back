<?php

use App\Http\Controllers\Api\SolicitudeController;
use App\Http\Controllers\DefenseController;
use App\Http\Controllers\DocOfController;
use App\Http\Controllers\DocResolutionController;
use App\Http\Controllers\HistoryReviewController;
use Illuminate\Support\Facades\Route;



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
Route::get('/oficio/descargar-aprobacion/informe/{office_id}', [DocOfController::class, 'downloadOfficeApproveInforme']);

//Ruta para ver resolucion de aprobacion de INFORME FINAL (APT) ---> ESTUDIANTE, PAISI, FACULTAD
Route::get('/resolucion/ver-aprobacion/informe/{resolution_id}', [DocResolutionController::class, 'viewResApproveInforme']);
Route::get('/resolucion/download-aprobacion/informe/{resolution_id}', [DocResolutionController::class, 'downloadResApproveInforme']);

//Ruta para ver oficio de aprobacion de DECLARAR APTO PARA LA SUSTENTACION ---> ESTUDIANTE, PAISI, FACULTAD
Route::get('/oficio/ver/declarar-apto/{office_id}', [DocOfController::class, 'viewOfficeDeclareApto']);
Route::get('/oficio/descargar/declarar-apto/{office_id}', [DocOfController::class, 'downloadOfficeDeclareApto']);

//Ruta para ver resolucion de aprobacion de DECLARAR APTO PARA LA SUSTENTACION ---> ESTUDIANTE, PAISI, FACULTAD
Route::get('/resolucion/ver/declarar-apto/{resolution_id}', [DocResolutionController::class, 'viewResDeclareApto']);
Route::get('/resolucion/descargar/declarar-apto/{resolution_id}', [DocResolutionController::class, 'downloadResDeclareApto']);

//Ruta para ver oficio de aprobacion de DECLARAR APTO PARA LA SUSTENTACION ---> ESTUDIANTE, PAISI, FACULTAD
Route::get('/oficio/ver/desigancion-fecha-hora-sustentacion/{office_id}', [DocOfController::class, 'viewOfficeDesignationDate']);
Route::get('/oficio/descargar/desigancion-fecha-hora-sustentacion/{office_id}', [DocOfController::class, 'downloadOfficeDesignationDate']);

//Ruta para ver resolucion de aprobacion de DECLARAR APTO PARA LA SUSTENTACION ---> ESTUDIANTE, PAISI, FACULTAD
Route::get('/resolucion/ver/desigancion-fecha-hora-sustentacion/{resolution_id}', [DocResolutionController::class, 'viewResDesignationDate']);
Route::get('/resolucion/descargar/desigancion-fecha-hora-sustentacion/{resolution_id}', [DocResolutionController::class, 'downloadResDesignationDate']);

Route::get('/sustentacion/ver-acta/{sustentacion_id}', [DefenseController::class, 'viewActDefense']);
Route::get('/sustentacion/descargar-acta/{sustentacion_id}', [DefenseController::class, 'downloadActDefense']);

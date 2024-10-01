<?php

namespace App\Http\Controllers;

use App\Models\DocOf;
use App\Models\DocResolution;
use App\Models\HistoryReview;
use App\Models\Review;
use App\Models\Solicitude;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function createReview($student_id)
{
   // Obtener la solicitud del estudiante
   $solicitude = Solicitude::where('student_id', $student_id)->first();

   // Verificar que la solicitud existe
   if (!$solicitude) {
       return response()->json([
           'status' => false,
           'message' => 'Solicitud no encontrada para este estudiante.'
       ], 404);
   }

   // Obtener el docof relacionado
   $docof = DocOf::where('solicitude_id', $solicitude->id)->first();

   // Verificar que el docof existe
   if (!$docof) {
       return response()->json([
           'status' => false,
           'message' => 'Documento de officio no encontrado.'
       ], 404);
   }

   // Obtener la resolución del documento utilizando docof_id
   $docresolution = DocResolution::where('docof_id', $docof->id)->first();

    // Verificar que la resolución exista y esté en estado 'tramitado'
    if ($docresolution->docres_status =! 'tramitado') {
        return response()->json([
            'status' => false,
            'message' => 'No cuenta con resolución de designación de asesor.'
        ], 400);
    }


    // Contar cuántas revisiones tiene el estudiante
    $count = HistoryReview::where('student_id', $student_id)->count();

    // Crear la revisión
    $review = Review::create([
        'student_id' => $student_id,
        'adviser_id' => $solicitude->adviser_id, // Accediendo a adviser_id desde la relación
        'rev_count' => $count + 1, // Incrementa el contador de revisiones
        'rev_file' => null,
        'rev_status' => 'pendiente',
        'rev_type' => 'tesis'
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Su solicitud de revisión fue enviada',
        'data' => $review
    ], 201);
}

    
}

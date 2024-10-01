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
            'message' => 'Documento de oficio no encontrado.'
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

        // Crear la revisión
        Review::create([
            'student_id' => $student_id,
            'adviser_id' => $solicitude->adviser_id, // Accediendo a adviser_id desde la relación
            'rev_count' => 1, // Incrementa el contador de revisiones
            'rev_file' => null,
            'rev_status' => 'pendiente',
            'rev_type' => 'tesis'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Su solicitud de revisión fue enviada',
        ], 201);
    }

    public function updateStatusReview($student_id, Request $request) {
        // Encuentra la revisión correspondiente al student_id
        $review = Review::where('student_id', $student_id)->first();
    
        // Verifica si se encontró la revisión
        if ($review) {
            // Define las reglas de validación
            $rules = [
                'rev_status' => 'required|string|in:pendiente,aprobado,observado',
                'rev_num_of' => 'nullable|string',
                'rev_file' => 'nullable|string'
            ];
    
            // Contar cuántas revisiones tiene el estudiante
            $count = HistoryReview::where('student_id', $student_id)->count();
    
            // Valida los datos de entrada
            $this->validate($request, $rules);
    
            $status = $request->input('rev_status'); // Cambia aquí
    
            // Determina el nuevo estado basado en el valor de $status
            switch ($status) {
                case 'pendiente':
                    $review->update([
                        'rev_status' => 'pendiente',
                    ]);
                    return response()->json(['message' => 'Solicitud de revision enviada'], 200);

                    break;
    
                case 'aprobado':
                    $rules['rev_num_of'] = 'required|string'; // Agrega la regla para rev_num_of
                    $this->validate($request, ['rev_num_of' => $rules['rev_num_of']]);
    
                    HistoryReview::create([
                        'adviser_id' => $review->adviser_id,
                        'student_id' => $review->student_id,
                        'rev_num_of' => $request->input('rev_num_of'), // Cambia aquí
                        'rev_count' => $count + 1,
                        'rev_status' => 'aprobado', // Estado
                        'rev_type' => 'tesis',
                    ]);
    
                    $review->update([
                        'rev_status' => 'aprobado',
                    ]);

                    return response()->json(['message' => 'Su confomidad fué enviada'], 200);

                    break;
    
                case 'observado':
                    $rules['rev_file'] = 'required|string'; // Agrega la regla para rev_file
                    $this->validate($request, ['rev_file' => $rules['rev_file']]);
    
                    HistoryReview::create([
                        'adviser_id' => $review->adviser_id,
                        'student_id' => $review->student_id,
                        'rev_count' => $count + 1,
                        'rev_file' => $request->input('rev_file'), // Cambia aquí
                        'rev_status' => 'observado', // Estado
                        'rev_type' => 'tesis',
                    ]);
    
                    // Actualiza la revisión
                    $review->update([
                        'rev_count' => $count + 1,
                        'rev_status' => 'observado',
                    ]);
                    
                    return response()->json(['message' => 'Observacion enviada'], 200);
                    
                    break;
    
                default:
                    return response()->json(['message' => 'Estado no válido.'], 400);
            }
    
            return response()->json(['message' => 'Estado de la revisión actualizado correctamente.'], 200);
        } else {
            return response()->json(['message' => 'Revisión no encontrada.'], 404);
        }
    }
    


    
}

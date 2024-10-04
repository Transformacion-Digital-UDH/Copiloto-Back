<?php

namespace App\Http\Controllers;

use App\Models\DocOf;
use App\Models\DocResolution;
use App\Models\HistoryReview;
use App\Models\Review;
use App\Models\Solicitude;
use App\Models\Student;
use Carbon\Carbon;
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
            'message' => 'No uenta con resolución de designación de asesor.'
        ], 400);
    }
    
    $review_student = Review::where('student_id', $solicitude->student_id)->first();
    $review_adviser = Review::where('student_id', $solicitude->student_id)->first();

    if ($review_student and $review_adviser){
        return response()->json([
            'status' => false,
            'message' => 'Ya tiene una revision pendiente.'
        ], 400);
    }
        // Crear la revisión
        Review::create([
            'student_id' => $student_id,
            'adviser_id' => $solicitude->adviser_id, // Accediendo a adviser_id desde la relación
            'rev_count' => 1, // Incrementa el contador de revisiones
            'rev_file' => null,
            'rev_status' => 'pendiente',
            'rev_type' => 'tesis',
            'rev_adviser_rol' => 'asesor', // asesor, presidente, secretario, vocal

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
                        'rev_type' => $review->rev_type,
                        'rev_adviser_rol' => $review->rev_adviser_rol, // asesor, presidente, secretario, vocal
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
                        'rev_type' => $review->rev_type,
                        'rev_adviser_rol' => $review->rev_adviser_rol, // asesor, presidente, secretario, vocal
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
    

    public function viewRevisionByAdviser($adviser_id) {
    // Obtiene las revisiones del asesor
    $reviews = Review::where('adviser_id', $adviser_id)
                        ->where('rev_adviser_rol', 'asesor')
                        ->get();


    // Mapear los resultados asignando prioridad al estado usando sortBy
    $sortedReviews = $reviews->sortBy(function ($review) {
        switch ($review->rev_status) {
            case 'pendiente':
                return 1;
            case 'observado':
                return 2;
            case 'aprobado':
                return 3;
            default:
                return 4;  // Si hay un estado no esperado
        }
    });

    // Array donde almacenaremos los resultados formateados
    $result = [];

    foreach ($sortedReviews as $review) {
        // Obtener la solicitud y el estudiante relacionado a la revisión actual
        $solicitude = Solicitude::where('student_id', $review->student_id)->first();
        $student = Student::where('_id', $review->student_id)->first();

        // Manejar casos donde el estudiante no exista
        if (!$student) {
            continue;
        }

        // Formatear el nombre del estudiante correctamente
        $studentName = $student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name;

        // Agregar los datos de la revisión al array resultante
        $result[] = [
            'stu_id' => $student->_id,
            'stu_name' => $studentName,
            'sol_title_inve' => $solicitude ? $solicitude->sol_title_inve : 'No title', // Maneja si no hay solicitud
            'rev_count' => $review->rev_count,
            'rev_status' => $review->rev_status,
            'link-tesis' => $solicitude->document_link,
            'updated_at' => $review->updated_at ? Carbon::parse($review->updated_at)->format('d/m/Y | H:i:s') : null,
            'rol_revisor' => $review->rev_adviser_rol,
        ];
    }

    // Retorna los datos ordenados y con los índices reorganizados
    return response()->json([
        'data' => array_values($result),  // Asegúrate de devolver los datos con índices reorganizados
    ], 200);
}
}

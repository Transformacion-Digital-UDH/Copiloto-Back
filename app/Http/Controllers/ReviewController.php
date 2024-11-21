<?php

namespace App\Http\Controllers;

use App\Models\Adviser;
use App\Models\Defense;
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
        $review = Review::where('student_id', $student_id)
                        ->where('rev_adviser_rol', 'asesor')
                        ->where('rev_type', 'tesis')
                        ->first();
    
        // Verifica si se encontró la revisión
        if ($review) {
            // Define las reglas de validación
            $rules = [
                'rev_status' => 'required|string|in:pendiente,aprobado,observado',
                'rev_num_of' => 'nullable|string',
            ];
    
            // Contar cuántas revisiones tiene el estudiante
            $count = HistoryReview::where('student_id', $student_id)
                                ->where('adviser_id', $review->adviser_id)    
                                ->where('rev_adviser_rol', $review->rev_adviser_rol)    
                                ->where('rev_type', $review->rev_type)    
                                ->count();
    
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
                        'rev_count' => $count + 1,
                        'rev_num_of' => $request->input('rev_num_of'), // Cambia aquí
                        'rev_status' => 'aprobado',
                    ]);

                    return response()->json(['message' => 'Su confomidad fué enviada'], 200);

                    break;
    
                case 'observado':
    
                    HistoryReview::create([
                        'adviser_id' => $review->adviser_id,
                        'student_id' => $review->student_id,
                        'rev_count' => $count + 1,
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
                        ->where('rev_type', 'tesis')
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
            'solicitude_id' => $solicitude->_id,
            'review_id' => $review->_id,
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

    public function viewReviewAsJuryForAdviser($adviser_id)
    {
        // Obtener todas las revisiones con adviser_id y los roles específicos
        $reviews = Review::where('adviser_id', $adviser_id)
                        ->where('rev_type', 'tesis')
                        ->whereIn('rev_adviser_rol', ['presidente', 'vocal', 'secretario'])
                        ->get();

        // Si no hay revisiones, devolver un mensaje de error
        if ($reviews->isEmpty()) {
            return response()->json(['message' => 'No tiene revisiones pendientes'], 404);
        }

        // Definir el orden deseado para rev_status
        $statusOrder = ['pendiente', 'observado', 'aprobado'];

        // Ordenar las revisiones por rev_status según el orden personalizado
        $sortedReviews = $reviews->sort(function ($a, $b) use ($statusOrder) {
            $posA = array_search($a->rev_status, $statusOrder);
            $posB = array_search($b->rev_status, $statusOrder);
            return $posA - $posB;
        })->values(); // Resetear las claves

        // Crear una estructura para devolver los resultados con el nombre del estudiante
        $response = [];

        // Recorrer las revisiones ordenadas y buscar al estudiante
        foreach ($sortedReviews as $review) {
            // Buscar el estudiante por su ID
            $my_role = Review::where('adviser_id', $review->adviser_id)->first();
            $student = Student::where('_id', $review->student_id)->first();
            $solicitude = Solicitude::where('student_id', $student->_id)->first();
            $review_presidente = Review::where('student_id', $student->_id)->where('rev_adviser_rol', 'presidente')->where('rev_type', 'tesis')->first();
            $review_secretario = Review::where('student_id', $student->_id)->where('rev_adviser_rol', 'secretario')->where('rev_type', 'tesis')->first();
            $review_vocal = Review::where('student_id', $student->_id)->where('rev_adviser_rol', 'vocal')->where('rev_type', 'tesis')->first();

            // Asegurarse de que el estudiante exista
            if ($student) {
                $response[] = [
                    'nombre' => strtoupper($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name), 
                    'titulo' => $solicitude->sol_title_inve,             
                    'mi_rol' => $my_role->rev_adviser_rol,             
                    'link' => $solicitude->informe_link,
                    'estado' => $review->rev_status, 
                    'revision_id' => $review->_id, 
                    'rol' => $review->rev_adviser_rol,
                    'count' => $review->rev_count,
                    'presidente_estado' => $review_presidente->rev_status,
                    'presidente_cont' => $review_presidente->rev_count,
                    'secretario_estado' => $review_secretario->rev_status,
                    'secretario_cont' => $review_secretario->rev_count,
                    'vocal_estado' => $review_vocal->rev_status,
                    'vocal_cont' => $review_vocal->rev_count,
                ];
            } 
        }
        
        // Devolver las revisiones ordenadas con el nombre del estudiante
        return response()->json([
            'data' => $response
        ], 200);
    }

    public function getInfoReviewJuriesByStudent($student_id){

        // Obtener todas las revisiones con adviser_id y los roles específicos
        $reviews = Review::where('student_id', $student_id)
                        ->where('rev_type', 'tesis')
                        ->whereIn('rev_adviser_rol', ['presidente', 'vocal', 'secretario'])
                        ->get();

        $docof = DocOf::where('student_id', $student_id)
                    ->where('of_name', 'Solicitud de jurados para revision de tesis')    
                    ->first();

        if ($reviews->isEmpty()) {
            return response()->json(['message' => 'No tiene jurados'], 404);
        }

        $response = [];

        foreach ($reviews as $review) {
                // Buscar el estudiante por su ID
                $adviser = Adviser::where('_id', $review->adviser_id)->first();

                // Asegurarse de que el estudiante exista
                if ($adviser) {
                    $response[] = [
                        'revision_id' => $review->_id, 
                        'nombre' => strtoupper($adviser->adv_lastname_m . ' ' . $adviser->adv_lastname_f . ', ' . $adviser->adv_name), 
                        'rol' => $review->rev_adviser_rol, 
                        'numero_revision' => $review->rev_count, 
                        'fecha' => $review->updated_at ? Carbon::parse($review->updated_at)->format('d/m/Y | H:i:s') : null,
                        'estado' => $review->rev_status,
                    ];
                } 
            }

        $solicitude = Solicitude::where('student_id', $student_id)->first();

        $presidente = Review::where('student_id', $student_id)
                        ->where('rev_type', 'tesis')
                        ->where('rev_adviser_rol', 'presidente')
                        ->first();
        $secretario = Review::where('student_id', $student_id)
                        ->where('rev_type', 'tesis')
                        ->where('rev_adviser_rol', 'secretario')
                        ->first();
        $vocal = Review::where('student_id', $student_id)
                        ->where('rev_type', 'tesis')
                        ->where('rev_adviser_rol', 'vocal')
                        ->first();

        if($presidente->rev_status==$secretario->rev_status && $secretario->rev_status==$vocal->rev_status)
        {
            $status = $presidente->rev_status;
        }

        else{

            $status = 'observado';
        }

        return response()->json([
            'estudiante_id' => $solicitude->student_id,
            'titulo' => $solicitude->sol_title_inve,
            'link' => $solicitude->document_link,
            'oficio_id' => $docof->_id,
            'oficio_estado' => $docof->of_status,
            'estado_general' => $status,
            'data' => $response
        ], 200);
    }

    public function updateStatusReviewJuries($review_id, Request $request){
        // Encuentra la revisión correspondiente al student_id
        $review = Review::where('_id', $review_id)
                        ->first();
    
        // Verifica si se encontró la revisión
        if ($review) {
            // Define las reglas de validación
            $rules = [
                'rev_status' => 'required|string|in:pendiente,aprobado,observado,calificado',
                'rev_num_of' => 'nullable|string',
            ];
    
            // Contar cuántas revisiones tiene el estudiante
            $count = HistoryReview::where('student_id', $review->student_id)
                                ->where('adviser_id', $review->adviser_id)    
                                ->where('rev_type', $review->rev_type)   
                                ->count();
    
            // Valida los datos de entrada
            $this->validate($request, $rules);
    
            $status = $request->input('rev_status'); // Cambia aquí
    
            // Determina el nuevo estado basado en el valor de $status
            switch ($status) {
                case 'pendiente':
                    $review->update([
                        'rev_status' => 'pendiente',
                    ]);
                    return response()->json([
                        'estado' => $review->rev_status,
                        'message' => 'Solicitud de revision enviada'
                    ], 200);

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
                        'rev_count' => $count + 1,
                        'rev_num_of' => $request->input('rev_num_of'), // Cambia aquí
                        'rev_status' => 'aprobado',
                    ]);

                    return response()->json([
                        'estado' => $review->rev_status,
                        'message' => 'Su confomidad fué enviada'
                    ], 200);

                    break;
    
                case 'observado':
    
                    HistoryReview::create([
                        'adviser_id' => $review->adviser_id,
                        'student_id' => $review->student_id,
                        'rev_count' => $count + 1,
                        'rev_status' => 'observado', // Estado
                        'rev_type' => $review->rev_type,
                        'rev_adviser_rol' => $review->rev_adviser_rol, // asesor, presidente, secretario, vocal
                    ]);
    
                    // Actualiza la revisión
                    $review->update([
                        'rev_count' => $count + 1,
                        'rev_status' => 'observado',
                    ]);
                    
                    return response()->json([
                        'estado' => $review->rev_status,
                        'message' => 'Observacion enviada'
                    ], 200);
                    
                    break;

                case 'calificado':

                    $rules['rev_score'] = 'required|string'; // Agrega la regla para rev_num_of
                    
                    $this->validate($request, ['rev_score' => $rules['rev_score']]);

                    // Actualiza la revisión
                    $review->update([
                        'rev_status' => 'calificado',
                        'rev_score' => $request->input('rev_score'),
                    ]);

                    return response()->json([
                        'estado' => $review->rev_status,
                        'message' => 'calificado con éxito'
                    ], 200);

                    break;
    
                default:
                    return response()->json(['message' => 'Estado no válido.'], 400);
            }
            return response()->json(['message' => 'Estado de la revisión actualizado correctamente.'], 200);
        } else {
            return response()->json(['message' => 'Revisión no encontrada.'], 404);
        }
    }
    public function createReviewInforme($student_id)
    {
    // Obtener la solicitud del estudiante
    $sol_da = Solicitude::where('student_id', $student_id)->first();

    // Verificar que la solicitud existe
    if ($sol_da === null || $sol_da->informe_link === null) {
        return response()->json([
            'estado' => 'no iniciado',
            'message' => 'Espere a que su link de informe sea creado o llame a su escuela académica.'
        ], 404);
    }

    $review = Review::where('student_id', $student_id)
                    ->where('adviser_id', $sol_da->adviser_id)
                    ->where('rev_type', 'informe')
                    ->first();

    if ($review){
        return response()->json([
            'estado' => 'pendiente',
            'message' => 'Ya tiene una revisión pendiente de su asesor'
        ], 404);
    }
    
    // Crear la revisión
        Review::create([
            'student_id' => $student_id,
            'adviser_id' => $sol_da->adviser_id, // Accediendo a adviser_id desde la relación
            'rev_count' => 1, // Incrementa el contador de revisiones
            'rev_status' => 'pendiente',
            'rev_type' => 'informe',
            'rev_adviser_rol' => 'asesor', // asesor, presidente, secretario, vocal

        ]);

        return response()->json([
            'estado' => 'pendiente',
            'message' => 'Su solicitud de revisión fue enviada a su asesor',
        ], 201);
    }


    public function getInfoConfAdviserInforme($adviser_id) {
        // Obtiene las revisiones del asesor
        $reviews = Review::where('adviser_id', $adviser_id)
                            ->where('rev_adviser_rol', 'asesor')
                            ->where('rev_type', 'informe')
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
                'revision_id' => $review->_id,
                'estudiante_id' => $student->_id,
                'estudiante_nombre' => $studentName,
                'titulo' => $solicitude ? $solicitude->sol_title_inve : 'No title', // Maneja si no hay solicitud
                'contador' => $review->rev_count,
                'revision_estado' => $review->rev_status,
                'link-informe' => $solicitude->informe_link,
                'actualizado' => $review->updated_at ? Carbon::parse($review->updated_at)->format('d/m/Y | H:i:s') : null,
                'rol' => $review->rev_adviser_rol,
            ];
        }
    
        // Retorna los datos ordenados y con los índices reorganizados
        return response()->json([
            'data' => array_values($result),  // Asegúrate de devolver los datos con índices reorganizados
        ], 200);
    }

    public function getInfoDefenseAdviser($adviser_id) {
        // Obtiene las revisiones del asesor
        $reviews = Review::where('adviser_id', $adviser_id)
                            ->where('rev_type', 'sustentacion')
                            ->get();
    
        // Mapear los resultados asignando prioridad al estado usando sortBy
        $sortedReviews = $reviews->sortBy(function ($review) {
            switch ($review->rev_status) {
                case 'pendiente':
                    return 1;
                case 'calificado':
                    return 2;
                default:
                    return 3;  // Si hay un estado no esperado
            }
        });
    
        // Array donde almacenaremos los resultados formateados
        $result = [];
    
        foreach ($sortedReviews as $review) {
            // Obtener la solicitud y el estudiante relacionado a la revisión actual
            $solicitude = Solicitude::where('student_id', $review->student_id)->first();
            $student = Student::where('_id', $review->student_id)->first();
            $defense = Defense::where('student_id',$review->student_id)->first();

            // Manejar casos donde el estudiante no exista
            if (!$student) {
                continue;
            }
    
            // Formatear el nombre del estudiante correctamente
            $studentName = $student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name;
    
            // Agregar los datos de la revisión al array resultante
            $result[] = [
                'revision_id' => $review->_id,
                'estudiante_id' => $student->_id,
                'estudiante_nombre' => $studentName,
                'titulo' => $solicitude ? $solicitude->sol_title_inve : 'No title', // Maneja si no hay solicitud
                'revision_estado' => $review->rev_status,
                'link_informe' => $solicitude->informe_link,
                'actualizado' => $review->updated_at ? Carbon::parse($review->updated_at)->format('d/m/Y | H:i:s') : null,
                'rol' => $review->rev_adviser_rol,
                'nota' => $review->rev_score ?? '',
                'sustentacion' => $defense->_id ?? '',
                'sustentacion_estado' => $defense->def_status ?? '',
            ];
        }
    
        // Retorna los datos ordenados y con los índices reorganizados
        return response()->json([
            'data' => array_values($result),  // Asegúrate de devolver los datos con índices reorganizados
        ], 200);
    }
}

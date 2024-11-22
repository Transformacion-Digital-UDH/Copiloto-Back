<?php

namespace App\Http\Controllers;

use App\Http\Resources\DocOfResource;
use App\Http\Resources\DocResolutionResource;
use App\Http\Resources\HistoryResource;
use App\Models\DocOf;
use App\Models\DocResolution;
use App\Models\Solicitude;
use App\Models\Student;
use App\Models\Adviser;
use App\Models\Defense;
use App\Models\Filter;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use MongoDB\Laravel\Eloquent\Casts\ObjectId;

class StudentController extends Controller
{
    public function getAll()
    {
        $student = Student::get()->toArray();
        return response()->json($student);
    }


        public function getInfoStudentById($student_id)
    {
        // Recibe el id del estudiante y busca
        $student = Student::where('_id', $student_id)->first();

        if (!$student) {
            return response()->json(['message' => 'El estudiante no existe'], 404);
        }

        // Obtener la ultima solicitud creada por el estudiante
        $solicitude = Solicitude::where('student_id', $student->_id)->first();

        if (!$solicitude) {
            return response()->json([
                'status' => false,
                'message' => 'Este estudiante no inició su trámite'
            ], 404);
        }
        
        $docof = DocOf::where('solicitude_id', $solicitude->_id)->first();
        $resolution = [];

        if (!$docof) {
            $office = [];
        } else {
            $office = new DocOfResource($solicitude->docof);
            $resdoc = DocResolution::where('docof_id', $docof->_id)->first();
            
            if ($resdoc) {
                $resolution = new DocResolutionResource($resdoc);
            }
        }

        // Obtener información del asesor
        $adviser = null;
        if ($solicitude->adviser_id) {
            $adviser = Adviser::find($solicitude->adviser_id); // Asegúrate de que Adviser sea el modelo correcto
        }

        // Devolver los datos del estudiante junto con sus solicitudes
        return response()->json([
            'status' => true,
            'solicitud' => [
                "id" => $solicitude->_id,
                "titulo" => $solicitude->sol_title_inve,
                "asesor_id" => $solicitude->adviser_id,
                "estado" => $solicitude->sol_status,
                "observacion" => $solicitude->sol_observation,
                "link" => $solicitude->document_link,
                "tipo_investigacion" => $solicitude->sol_type_inve,
                "asesor" => $adviser ? [
                    "nombre" => $adviser->adv_name,
                    "apellido_paterno" => $adviser->adv_lastname_f,
                    "apellido_materno" => $adviser->adv_lastname_m,
                ] : null, // Si el asesor no se encuentra, retorna null
            ],
            'historial' => HistoryResource::collection($solicitude->history),
            'oficio' => $office,
            'resolucion' => $resolution
        ], 200);
    }

    public function viewJuriesForTesisByStudent($student_id) {
        // Obtener todas las revisiones relacionadas con el estudiante especificado
        $reviews = Review::where('student_id', $student_id)
            ->where('rev_adviser_rol', '!=', 'asesor')
            ->where('rev_type', 'tesis')
            ->get();  
    
        // Crear un array para almacenar asesores y roles
        $jurados = [];
    
        foreach ($reviews as $review) {
            // Obtener el asesor correspondiente a la revisión
            $adviser = Adviser::where('_id', $review->adviser_id)->first(); // Obtener el asesor
            
            // Verificar si el asesor existe
            $adviser_name = $adviser ? strtoupper($adviser->adv_lastname_m . ' ' . $adviser->adv_lastname_f . ', ' . $adviser->adv_name) : 'No disponible';
    
            // Agregar el asesor y su rol al array de jurados
            $jurados[] = [
                'asesor' => $adviser_name, // Convertir a mayúsculas
                'rol' => $review->rev_adviser_rol
            ];
        }
    
        // Obtener el documento de oficio
        $docof = DocOf::where('student_id', $student_id)
            ->where('of_name', 'Solicitud de jurados para revision de tesis')
            ->first();
    
        // Verificar si el documento existe antes de acceder a sus propiedades
        if (!$docof) {
            return response()->json([
                'estudiante_id' => $student_id,
                'mensaje' => 'No ha iniciado el trámite, complete el pre-requisito.',
                'estado' => 'No iniciado',
                'jurados' => $jurados,
            ], 404); // O cualquier otro código de estado que consideres apropiado
        }
    
        // Retornar los roles y IDs en formato JSON
        return response()->json([
            'estudiante_id' => $student_id,
            'tramite' => $docof->of_name,
            'estado' => $docof->of_status,
            'docof_id' => $docof->_id,
            'jurados' => $jurados,
        ], 200);
    }
    

    public function getInfoApproveThesis($student_id){
        $docof = DocOf::where('student_id', $student_id)
            ->where('of_name', 'Aprobación de tesis')
            ->first();

        if(!$docof){
            return response()->json([
                'mensaje' => 'Proceso no iniciado',
            ], 400);
        }

        $docres = DocResolution::where('docof_id', $docof->_id)
            ->first();

        if(!$docres){
            return response()->json([
                'estudiante_id' => $student_id,
                'oficio_id' => $docof->_id,
                'oficio_estado' => $docof->of_status,
                'resolucion_id' => $docres->_id ?? '',
                'resolucion_estado' => 'no iniciado',
            ], 200);
        }
        return response()->json([
            'estudiante_id' => $student_id,
            'oficio_id' => $docof->_id,
            'oficio_estado' => $docof->of_status,
            'resolucion_id' => $docres->_id,
            'resolucion_estado' => $docres->docres_status,
        ], 200);
    }

    public function getInfoEjecucion($student_id) {
        $student = Student::where('_id', $student_id)->first();
        $student_name = $student->stu_lastname_m . ' ' . $student->stu_lastname_f . ' ' . $student->stu_name;
        // Designacion de asesor
        $sol_da = Solicitude::where('student_id', $student_id)->first();
        $off_da = DocOf::where('solicitude_id', $sol_da->_id)->first();
        $res_da = DocResolution::where('docof_id', $off_da->_id)->first();

        $revision_presidente = Review::where('student_id', $student_id)->where('rev_adviser_rol', 'presidente')->where('rev_type', 'tesis')->first();
        $revision_secretario = Review::where('student_id', $student_id)->where('rev_adviser_rol', 'secretario')->where('rev_type', 'tesis')->first();
        $revision_vocal = Review::where('student_id', $student_id)->where('rev_adviser_rol', 'vocal')->where('rev_type', 'tesis')->first();
        $revision_asesor = Review::where('student_id', $student_id)->where('rev_adviser_rol', 'asesor')->where('rev_type', 'tesis')->first();

        $presidente = Adviser::where('_id', $revision_presidente->adviser_id)->first();
        $presidente = ucwords(strtolower($presidente->adv_name . ' ' . $presidente->adv_lastname_m . ' ' . $presidente->adv_lastname_f));

        $secretario = Adviser::where('_id', $revision_secretario->adviser_id)->first();
        $secretario = ucwords(strtolower($secretario->adv_name . ' ' . $secretario->adv_lastname_m . ' ' . $secretario->adv_lastname_f));

        $vocal = Adviser::where('_id', $revision_vocal->adviser_id)->first();
        $vocal = ucwords(strtolower($vocal->adv_name . ' ' . $vocal->adv_lastname_m . ' ' . $vocal->adv_lastname_f));

        $asesor = Adviser::where('_id', $revision_asesor->adviser_id)->first();
        $asesor = ucwords(strtolower($asesor->adv_name . ' ' . $asesor->adv_lastname_m . ' ' . $asesor->adv_lastname_f));

        
        //Designacion de jurados
        $off_dj = DocOf::where('student_id', $student_id)->where('of_name', 'Solicitud de jurados para revision de tesis')->first();
        $off_apt = DocOf::where('student_id', $student_id)->where('of_name', 'Aprobación de tesis')->first();
        $res_apt = DocResolution::where('docof_id', $off_apt->_id)->first();


        //Ini - End 
        $date_ini = Carbon::parse($res_da->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY'); 
        $date_end = Carbon::parse($res_da->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY'); 

        $res_emisor = 'Facultad de ingeniería';
        $off_emisor = 'Escuela académica de ingeniería de sistemas e informática';

        return response()->json([
            'nombre_estudiante' => $student_name,
            'fecha_ini' => $date_ini,
            'fecha_fin' => $date_end,
            'designacion_asesor' => [
                'nombre_doc' => 'Carta de aceptación',
                'doc_emisor' => $asesor ,
                'doc_fecha' => Carbon::parse($sol_da->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY') ,
                'doc_id' => $sol_da->_id,
                'nombre_res' => 'Resolución de designación de asesor',
                'res_emisor' => $res_emisor,
                'res_fecha' => Carbon::parse($res_da->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY') ,
                'res_id' => $res_da->_id,

            ],
            'conformidad_asesor' => [
                'nombre_doc' => 'Oficio de conformidad - Asesor',
                'doc_emisor' => $asesor ,
                'doc_fecha' => Carbon::parse($revision_asesor->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY') ,
                'doc_id' => $revision_asesor->_id,
            ],
            'desigancion_jurados' => [
                'nombre_doc' => 'Oficio múltiple de designación de jurados',
                'doc_emisor' => $off_emisor,
                'doc_fecha' => Carbon::parse($off_dj->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY') ,
                'doc_id' => $off_dj->_id,
            ],
            'conformidad_jurado_pres' => [
                'nombre_doc' => 'Oficio de conformidad - Presidente',
                'doc_emisor' => $presidente,
                'doc_fecha' => Carbon::parse($revision_presidente->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY') ,
                'doc_id' => $revision_presidente->_id,
            ],
            'conformidad_jurado_secre' => [
                'nombre_doc' => 'Oficio de conformidad - Secretario',
                'doc_emisor' => $secretario,
                'doc_fecha' => Carbon::parse($revision_secretario->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY') ,
                'doc_id' => $revision_secretario->_id,
            ],
            'conformidad_jurado_vocal' => [
                'nombre_doc' => 'Oficio de conformidad - Vocal',
                'doc_emisor' => $vocal,
                'doc_fecha' => Carbon::parse($revision_vocal->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY') ,
                'doc_id' => $revision_vocal->_id,
            ],
            'aprobacion_tesis' => [
                'nombre_doc' => 'Resolución de aprobación de trabajo de investigación (Tesis)',
                'doc_emisor' => $res_emisor,
                'doc_fecha' => Carbon::parse($res_apt->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY') ,
                'doc_id' => $res_apt->_id,
            ]
        ], 200);

    }

    public function getStudentsInforme()
    {
        $students = Student::all();
        $data = []; // Inicializar el array $data
    
        foreach ($students as $student) {
            
            // Obtener la primera solicitud asociada al estudiante, si existe
            $sol_da = Solicitude::where('student_id', $student->_id)->first();
    
            // Verificar y obtener los datos del asesor si la solicitud existe
            $asesor = '';
            if ($sol_da && $sol_da->adviser_id) {
                $adviser = Adviser::find($sol_da->adviser_id);
                if ($adviser) {
                    $asesor = strtoupper("{$adviser->adv_name} {$adviser->adv_lastname_m} {$adviser->adv_lastname_f}");
                }
            }
    
            // Establecer el estado en 'pendiente' si no hay solicitud o falta el enlace de documento
            $status = ($sol_da && $sol_da->informe_link) ? 'generado' : 'pendiente';
    
            // Obtener la aprobación de tesis, si existe
            $off_apt = DocOf::where('student_id', $student->_id)->where('of_name', 'Aprobación de tesis')->first();
            $res_apt = $off_apt ? DocResolution::where('docof_id', $off_apt->_id)->first() : null;
    
            // Solo agregar al array si se encuentra una resolución o un registro válido
            if ($res_apt) {
                $data[] = [
                    'id_' => $student->_id,
                    'id_solicitud' => $sol_da->_id,
                    'informe_link' => $sol_da->informe_link,
                    'nombre' => "{$student->stu_name} {$student->stu_lastname_m} {$student->stu_lastname_f}",
                    'asesor' => $asesor,
                    'estado' => $status,
                    'resolucion_id' => $res_apt->_id,
                    'resolucion_fecha' => Carbon::parse($res_apt->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY'),
                    // Agrega aquí los demás campos que desees incluir
                ];
            }
        }
    
        return response()->json([
            'students' => $data
        ]);
    }
    

    public function getInfoConfAdviserInforme($student_id) {
        // Obtener la solicitud asociada al estudiante
        $solicitude = Solicitude::where('student_id', $student_id)->first();
    
        // Verificar si la solicitud existe
        if (!$solicitude) {
            return response()->json(['error' => 'Solicitud no encontrada'], 404);
        }
    
        // Obtener el asesor
        $adviser = Adviser::where('_id', $solicitude->adviser_id)->first();
        
        // Verificar si el asesor existe
        if (!$adviser) {
            return response()->json(['error' => 'Asesor no encontrado'], 404);
        }
    
        $adviser_name = ucwords(strtolower($adviser->adv_name . ' ' . $adviser->adv_lastname_m . ' ' . $adviser->adv_lastname_f));
        
        // Obtener la revisión
        $review = Review::where('student_id', $student_id)
                        ->where('adviser_id', $solicitude->adviser_id)    
                        ->where('rev_type', 'informe')    
                        ->first();
    
        // Construir la respuesta
        return response()->json([
            'asesor' => $adviser_name,
            'titulo' => $solicitude->sol_title_inve,
            'link-informe' => $solicitude->informe_link,
            'revision' => $review ? [
                'rev_id' => $review->_id,
                'rev_contador' => $review->rev_count,
                'rev_estado' => $review->rev_status,
                'rev_update' => Carbon::parse($review->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY'),
            ] : null,
        ]);
    }
    
    
    public function infoOfficeJuriesForInforme($student_id) {
        // Obtener el oficio
        $office = DocOf::where('student_id', $student_id)
                       ->where('of_name', 'designacion de jurados para revision de informe final')
                       ->first();
                       
        // Si el oficio no se encuentra, inicializa un objeto vacío
        $office_id = $office->_id ?? '';
        $office_status = $office->of_status ?? '';
    
        // Obtener la resolución asociada al oficio
        $resolution = DocResolution::where('docof_id', $office_id)->first();
        $resolution_id = $resolution->_id ?? '';
        $resolution_status = $resolution->docres_status ?? '';
    
        // Obtener revisiones de presidente, secretario y vocal
        $revision_presidente = Review::where('student_id', $student_id)
                                     ->where('rev_adviser_rol', 'presidente')
                                     ->where('rev_type', 'informe')
                                     ->first();
        $revision_secretario = Review::where('student_id', $student_id)
                                     ->where('rev_adviser_rol', 'secretario')
                                     ->where('rev_type', 'informe')
                                     ->first();
        $revision_vocal = Review::where('student_id', $student_id)
                                ->where('rev_adviser_rol', 'vocal')
                                ->where('rev_type', 'informe')
                                ->first();
    
        // Obtener nombres de los asesores o asignar vacío si no existen
        $presidente = $revision_presidente ? Adviser::where('_id', $revision_presidente->adviser_id)->first() : null;
        $presidente_nombre = $presidente ? ucwords(strtolower($presidente->adv_name . ' ' . $presidente->adv_lastname_m . ' ' . $presidente->adv_lastname_f)) : '';
    
        $secretario = $revision_secretario ? Adviser::where('_id', $revision_secretario->adviser_id)->first() : null;
        $secretario_nombre = $secretario ? ucwords(strtolower($secretario->adv_name . ' ' . $secretario->adv_lastname_m . ' ' . $secretario->adv_lastname_f)) : '';
    
        $vocal = $revision_vocal ? Adviser::where('_id', $revision_vocal->adviser_id)->first() : null;
        $vocal_nombre = $vocal ? ucwords(strtolower($vocal->adv_name . ' ' . $vocal->adv_lastname_m . ' ' . $vocal->adv_lastname_f)) : '';
    
        // Retornar respuesta con valores por defecto en blanco donde no se encuentren datos
        return response()->json([
            'estudiante_id' => $student_id,
            'presidente' => [
                'rol' => $revision_presidente->rev_adviser_rol ?? '',
                'nombre' => $presidente_nombre,
            ],
            'secretario' => [
                'rol' => $revision_secretario->rev_adviser_rol ?? '',
                'nombre' => $secretario_nombre,
            ],
            'vocal' => [
                'rol' => $revision_vocal->rev_adviser_rol ?? '',
                'nombre' => $vocal_nombre,
            ],
            'oficio' => [
                'of_id' => $office_id,
                'of_estado' => $office_status,
            ],
            'resolucion' => [
                'of_id' => $resolution_id,
                'of_estado' => $resolution_status,
            ],
        ]);
    }
    
    public function getReviewJuriesInforme($student_id){

        // Obtener todas las revisiones con adviser_id y los roles específicos
        $reviews = Review::where('student_id', $student_id)
                        ->where('rev_type', 'informe')
                        ->whereIn('rev_adviser_rol', ['presidente', 'vocal', 'secretario'])
                        ->get();

        $docof = DocOf::where('student_id', $student_id)
                    ->where('of_name', 'designacion de jurados para revision de informe final')    
                    ->first();

        $docres = DocResolution::where('docof_id', $docof->_id)
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
                        ->where('rev_type', 'informe')
                        ->where('rev_adviser_rol', 'presidente')
                        ->first();
        $secretario = Review::where('student_id', $student_id)
                        ->where('rev_type', 'informe')
                        ->where('rev_adviser_rol', 'secretario')
                        ->first();
        $vocal = Review::where('student_id', $student_id)
                        ->where('rev_type', 'informe')
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
            'link' => $solicitude->informe_link,
            'oficio_id' => $docof->_id ?? '',
            'oficio_estado' => $docof->of_status ?? '',
            'resolucion_id' => $docres->_id ?? '',
            'resolucion_estado' => $docres->docres_status ?? '',
            'estado_general' => $status,
            'data' => $response
        ], 200);
    }

    public function getInfoApproveInforme($student_id){
        $docof = DocOf::where('student_id', $student_id)
            ->where('of_name', 'Aprobación de informe')
            ->first();

        if(!$docof){
            return response()->json([
                'mensaje' => 'Proceso no iniciado',
            ], 400);
        }

        $docres = DocResolution::where('docof_id', $docof->_id)
            ->first();

        if(!$docres){
            return response()->json([
                'estudiante_id' => $student_id,
                'oficio_id' => $docof->_id,
                'oficio_estado' => $docof->of_status,
                'resolucion_id' => $docres->_id ?? '',
                'resolucion_estado' => 'no iniciado',
            ], 200);
        }
        return response()->json([
            'estudiante_id' => $student_id,
            'oficio_id' => $docof->_id,
            'oficio_estado' => $docof->of_status,
            'resolucion_id' => $docres->_id,
            'resolucion_estado' => $docres->docres_status,
        ], 200);
    }


    public function getInfoFilterStudent($student_id)
    {
        $student = Student::where('_id', $student_id)->first();

        if (!$student){
            return response()->json([
                'error' => 'Estudiante no existe'
            ], 404);
        }

        $filters = Filter::where('student_id', $student_id)->get();

        $filtrosPredefinidos = [
            'Primer filtro',
            'Segundo filtro',
            'Tercer filtro',
        ];

        $vri = [];

        foreach ($filtrosPredefinidos as $nombreFiltro) {
            $filter = $filters->firstWhere('fil_name', $nombreFiltro);

            $vri[] = [
                'fil_nombre' => $nombreFiltro,
                'fil_estado'  => $filter->fil_status ?? 'no iniciado',
                'fil_file'   => $filter->fil_file ?? '',
            ];
        }

        $stu_code = $student->stu_code;

        $response = Http::get("https://tucoach.udh.edu.pe/api/validar/{$stu_code}@udh.edu.pe");

        // Verifica si la solicitud fue exitosa antes de procesar la respuesta
        if (!$response->successful()) {

            // Devuelve un error si la solicitud no fue exitosa
            return response()->json([
                'error' => 'No se pudo conectar con la API'
            ], 500);
        }

        $data = $response->json();

        $tucoach = 'aprobado';

        if (!$data['status'] or $data['status'] === 'false') {
            $tucoach = 'pendiente';
        }

        return response()->json([
            'tu_coach' => [
                'doc_name' => 'Curso de buenas prácticas - TUCOACH.UDH',
                'doc_estado'=> $tucoach,
                'doc_ver' => $data['url'] ?? ''],
            'filtros' => $vri,

        ],200);
        
    }

    public function getInfoDeclareApto($student_id){
        $docof = DocOf::where('student_id', $student_id)
                    ->where('of_name', 'declaracion como apto')
                    ->first();

        if (!$docof){
            return response()->json([
                'estudiante_id' => $student_id ?? '',
                'oficio_id' => $docof->_id ?? '',
                'oficio_estado' => $docof->of_status ?? '',
                'resolucion_id' => $docres->_id ?? '',
                'resolucion_estado' => $docres->docres_status ?? '',
            ], 200);
        };

        $docres = DocResolution::where('docof_id', $docof->_id)
            ->first();

        return response()->json([
            'estudiante_id' => $student_id ?? '',
            'oficio_id' => $docof->_id ?? '',
            'oficio_estado' => $docof->of_status ?? '',
            'resolucion_id' => $docres->_id ?? '',
            'resolucion_estado' => $docres->docres_status ?? '',
        ], 200);
    }

    public function getInfoDesignationDate($student_id)
    {
        $docof = DocOf::where('student_id', $student_id)
                    ->where('of_name', 'designacion de fecha y hora')
                    ->first();

        // Verificar si existe el registro $docof
        if (!$docof) {
            return response()->json([
                'estado' => 'no iniciado',
                'error' => 'Oficio no encontrado, faltan requisitos'
            ], 404);
        }
       

        $docres = DocResolution::where('docof_id', $docof->_id)->first();

        if (!$docres) {
            return response()->json([
                'oficio_id' => $docof->_id ?? '',
                'oficio_estado' => $docof->of_status ?? '',
                'resolucion_id' => $docres->_id ?? '',
                'resolucion_estado' => $docres->docres_status ?? 'no iniciado',
                'error' => 'Su solicitud está en proceso'
            ], 400);
        }

        $rev_sus = Review::where('student_id', $student_id)
                    ->where('rev_type', 'sustentacion')
                    ->get();

        $data = [];
        foreach ($rev_sus as $rev) {
            $adviser = Adviser::where('_id', $rev->adviser_id)->first();
            if ($adviser) {
                $adv_full_name = strtoupper("{$adviser->adv_name} {$adviser->adv_lastname_m} {$adviser->adv_lastname_f}");
                $adv_role = $rev->rev_adviser_rol;

                $data[] = [
                    'asesor_nombre' => $adv_full_name,
                    'asesor_rol' => $adv_role,
                ];
            }
        }

        $sus = Defense::where('student_id', $student_id)->first();

        return response()->json([
            'data' => $data,
            'sus_fecha' => $sus->def_fecha ?? '',
            'sus_hora' => $sus->def_hora ?? '',
            'oficio_id' => $docof->_id ?? '',
            'oficio_estado' => $docof->of_status ?? 'no iniciado',
            'resolucion_id' => $docres->_id ?? '',
            'resolucion_estado' => $docres->docres_status ?? 'no iniciado',
        ], 200);
    }

    public function getInfoDefenseStudent($student_id){

        $sus = Defense::where('student_id', $student_id)->first();

        if(!$sus){
            return response()->json([
                'error' => 'este estudiante aun no tiene designacion de fecha y hora',
                'estado' => 'no iniciado',
                'status' => false,
            ], 404);
        }

        $nota = $sus->def_score;
        
        if($nota > 10){
            $nota_estado='APROBADO';
        }
        else{
            $nota_estado='DESAPROBADO';
        }

        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'Spanish_Spain', 'es');

        // Combinar fecha y hora
        $sus_fecha = strftime('%d de %B del %Y', strtotime($sus->updated_at));
        $sus_hora = strftime('%H:%M', strtotime($sus->updated_at));

        // Formatear el resultado completo
        $sus_fecha_hora = $sus_fecha . ' - ' . $sus_hora;

        return response()->json([
            'sus_id' => $sus->_id,
            'sus_nota' => $sus->def_score,
            'sus_fecha' => $sus_fecha_hora ?? '',
            'sus_estado' => $sus->def_status,
            'nota_estado' => $nota_estado ?? '',
        ], 200);
    }

}

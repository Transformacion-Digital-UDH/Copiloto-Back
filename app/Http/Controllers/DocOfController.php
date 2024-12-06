<?php

namespace App\Http\Controllers;

use App\Http\Resources\DocOfResource;
use App\Http\Controllers\GoogleDocumentController;
use App\Models\Adviser;
use App\Models\Defense;
use App\Models\DocOf;
use App\Models\DocResolution;
use App\Models\Faculty;
use App\Models\Filter;
use App\Models\Review;
use App\Models\Solicitude;
use App\Models\Student;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocOfController extends Controller
{
    protected $googleDocumentController;

    // Inyección de dependencia para GoogleDocumentController
    public function __construct(GoogleDocumentController $googleDocumentController)
    {
        $this->googleDocumentController = $googleDocumentController;
    }

    public function offPDF($id) {

        $office = DocOf::where('_id', $id)->first();
    
        // Verifica si el registro no se encuentra
        if (!$office) {
            return redirect()->back()->with('error', 'Solicitud no encontrada');
        }
        
        // Formatear la fecha updated_at como "11 de julio de 2024"
        $formattedDate = Carbon::parse($office->updated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
        $year = Carbon::parse($office->updated_at)->locale('es')->isoFormat('YYYY');



        $solicitude = Solicitude::where('_id', $office->solicitude_id)->first();
    
        // Recibe el id del Asesor
        $adviser = Adviser::where('_id', $solicitude->adviser_id)->first();
    
        // Verifica si el asesor existe
        if ($adviser) {
            // Formatear los nombres del asesor
            $adviserFormatted = [
                'adv_name' => ucwords(strtolower($adviser->adv_name)),
                'adv_lastname_m' => ucwords(strtolower($adviser->adv_lastname_m)),
                'adv_lastname_f' => ucwords(strtolower($adviser->adv_lastname_f)),
            ];
        } else {
            $adviserFormatted = null;
        }
        if ($adviser) {
            // Concatenar las primeras letras de los nombres y apellidos
            $siglas = strtoupper(
                mb_substr($adviser->adv_name, 0, 1) .
                mb_substr($adviser->adv_lastname_m, 0, 1) .
                mb_substr($adviser->adv_lastname_f, 0, 1)
            );
        } else {
            $siglas = null;
        }

        $student = Student::where('_id', $solicitude->student_id)->first();
    
        // Verifica si el estudiante existe
        if ($student) {
            // Formatear los nombres del estudiante
            $studentFormatted = [
                'stu_name' => ucwords(strtolower($student->stu_name)),
                'stu_lastname_m' => strtoupper($student->stu_lastname_m),
                'stu_lastname_f' => strtoupper($student->stu_lastname_f),
            ];
        } else {
            $studentFormatted = null; 
        }
    
        // Pasar los datos a la vista
        $pdf = Pdf::loadView('office_adviser', compact('siglas', 'office', 'formattedDate', 'adviserFormatted', 'studentFormatted', 'year'));
    
        return $pdf->stream(); // Puedes especificar un nombre para el archivo PDF
    }

    public function downloadOffice($id) {

        $office = DocOf::where('_id', $id)->first();
    
        // Verifica si el registro no se encuentra
        if (!$office) {
            return redirect()->back()->with('error', 'Solicitud no encontrada');
        }
        
        // Formatear la fecha updated_at como "11 de julio de 2024"
        $formattedDate = Carbon::parse($office->updated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
        $year = Carbon::parse($office->updated_at)->locale('es')->isoFormat('YYYY');



        $solicitude = Solicitude::where('_id', $office->solicitude_id)->first();
    
        // Recibe el id del Asesor
        $adviser = Adviser::where('_id', $solicitude->adviser_id)->first();
    
        // Verifica si el asesor existe
        if ($adviser) {
            // Formatear los nombres del asesor
            $adviserFormatted = [
                'adv_name' => ucwords(strtolower($adviser->adv_name)),
                'adv_lastname_m' => ucwords(strtolower($adviser->adv_lastname_m)),
                'adv_lastname_f' => ucwords(strtolower($adviser->adv_lastname_f)),
            ];
        } else {
            $adviserFormatted = null;
        }

        $student = Student::where('_id', $solicitude->student_id)->first();

        if ($adviser) {
            // Concatenar las primeras letras de los nombres y apellidos
            $siglas = strtoupper(
                mb_substr($adviser->adv_name, 0, 1) .
                mb_substr($adviser->adv_lastname_m, 0, 1) .
                mb_substr($adviser->adv_lastname_f, 0, 1)
            );
        } else {
            $siglas = null;
        }

        // Verifica si el estudiante existe
        if ($student) {
            // Formatear los nombres del estudiante
            $studentFormatted = [
                'stu_name' => ucwords(strtolower($student->stu_name)),
                'stu_lastname_m' => strtoupper($student->stu_lastname_m),
                'stu_lastname_f' => strtoupper($student->stu_lastname_f),
            ];
        } else {
            $studentFormatted = null; 
        }
    
        // Pasar los datos a la vista
        $pdf = Pdf::loadView('office_adviser', compact('siglas', 'office', 'formattedDate', 'adviserFormatted', 'studentFormatted', 'year'));
    
        return $pdf->download($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ' ' . $student->stu_name . ' OFF-DA.pdf'); // Puedes especificar un nombre para el archivo PDF
    }

    public function getOffices($facultad_id){

        $facultad = Faculty::where('_id', $facultad_id)->first();

        $students = Student::where('stu_faculty', $facultad->fa_faculty)->get();

        // // // Filtrar las oficios para que solo queden las asociadas a los estudiantes correctos
        // // $filteredOffices = $solicitudes->filter(function($solicitud) use ($students) {
            
        // //     // Comprobamos si el estudiante asociado tiene el programa correspondiente
        // //     return $students->contains(function($student) use ($solicitud) {
        // //         return $solicitud->student_id == $student->_id;
        // //     });
        // });

        // // Devolver las solicitudes filtradas
        // return DocOfResource::collection($filteredSolicitudes);
    }
    
    public function updateStatusPaisi(Request $request, $id)
    {
        // Validar la entrada
        $rules = [
            'of_status' => 'required|string|in:pendiente,observado,tramitado',
            'of_observation' => 'nullable|string',  // Solo obligatorio si es observado
            'of_num_of' => 'nullable|string',       // Solo obligatorio si es tramitado
            'of_num_exp' => 'nullable|string'       // Solo obligatorio si es tramitado
        ];

        // Si el estado es "observado", la observación debe ser obligatoria
        if ($request->input('of_status') === 'observado') {
            $rules['of_observation'] = 'required|string';
        }

        // Si el estado es "tramitado", of_num_of y of_num_exp deben ser obligatorios
        if ($request->input('of_status') === 'tramitado') {
            $rules['of_num_of'] = 'required|string';
            $rules['of_num_exp'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        // Buscar el oficio por ID
        $docOf = DocOf::find($id);

        if (!$docOf) {
            return response()->json([
                'status' => false,
                'message' => 'Oficio no encontrado'
            ], 404);
        }

        // Acciones en función del estado
        if ($request->input('of_status') === 'observado') {
            // Actualizar la observación y estado en el oficio
            $docOf->update([
                'of_status' => 'observado',
                'of_observation' => $request->input('of_observation')
            ]);

        } elseif ($request->input('of_status') === 'tramitado') {
            // Crear un nuevo registro en la colección docOf si no es "rechazado"
            $docResolution = new DocResolution([
                'docof_id' => $docOf->_id,
                'docres_name' => 'Resolución de designación de asesor',  // Inicializado como null
                'docres_num_res' => null,  // Inicializado como null
                'docres_status' => 'pendiente',  // Estado fijo en "pendiente"
                'docres_observation' => null  // Inicializado como null
            ]);
            $docResolution->save();

            // Asegurarse de que of_num_of y of_num_exp estén presentes
            $docOf->update([
                'of_name' => 'Solicitud de resolución de designación de asesor',
                'of_status' => 'tramitado',
                'of_observation' => null,
                'of_num_of' => $request->input('of_num_of'),
                'of_num_exp' => $request->input('of_num_exp')
            ]);

        }

        return response()->json([
            'status' => true,
            'message' => 'Estado del oficio actualizado correctamente',
            'docOf' => $docOf
        ], 200);
    }

    public function soliciteJuriesForTesis($student_id)
    {
        $conf_adviser = Review::where('student_id', $student_id)
                        ->where('rev_adviser_rol', 'asesor')
                        ->first();

        $search_docOf = DocOf::where('student_id', $student_id)
                        ->where('of_name', 'Solicitud de jurados para revision de tesis')
                        ->first();

        // Verificar si existe el asesor y si su estado es aprobado
        if ($conf_adviser == null || $conf_adviser->rev_status != 'aprobado') {
            return response()->json([
                'estado' => '',
                'message' => 'Este estudiante aun no tiene su conformidad por el asesor.'
            ], 404);
        }

        // Verificar si ya existe una solicitud de jurados
        if ($search_docOf != null && $search_docOf->of_name == 'Solicitud de jurados para revision de tesis') {
            return response()->json([
                'estado' => 'pendiente',
                'message' => 'Este estudiante ya tiene una solicitud en proceso.'
            ], 404);
        }

        // Crear una nueva solicitud
        $docOf = new DocOf([
            'student_id' => $student_id,
            'of_name' => 'Solicitud de jurados para revision de tesis',  
            'of_num_of' => null,  
            'of_num_exp' => null,  
            'of_status' => 'pendiente',  
            'of_observation' => null,  
        ]);

        // Guardar el nuevo documento en la base de datos
        $docOf->save();

        return response()->json([
            'estado' => $docOf->of_status,
            'status' => true,
            'message' => 'Solicitud enviada correctamente',
            'docOf' => $docOf
        ], 200);
    }

    public function viewSolicitudeOfJuries()
    {
        // Obtener todas las solicitudes con el nombre 'Solicitud de jurados para revisión de tesis'
        $solicitude_docof = DocOf::where('of_name', 'Solicitud de jurados para revision de tesis')->get();

        // Definir el orden deseado
        $order = ['pendiente', 'observado', 'tramitado'];

        // Ordenar manualmente las solicitudes por 'of_status'
        $sortedSolicitudes = $solicitude_docof->sort(function ($a, $b) use ($order) {
            return array_search($a->of_status, $order) <=> array_search($b->of_status, $order);
        })->values(); // Para asegurar que se mantenga como una colección indexada.

        // Crear un array para almacenar los resultados finales
        $result = [];

        // Recorrer cada solicitud ordenada y obtener los datos del estudiante y de la solicitud
        foreach ($sortedSolicitudes as $solicitude) {
            // Obtener el estudiante relacionado
            $student = Student::find($solicitude->student_id);
            
            // Obtener la solicitud relacionada
            $tittle = Solicitude::where('student_id', $solicitude->student_id)->first();
            
            // Si los datos existen, agregar al resultado
            if ($student && $tittle) {
                $result[] = [
                    'oficio_id' => $solicitude->_id,
                    'estado' => $solicitude->of_status,
                    'nombre' => $student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name,
                    'titulo' => $tittle->sol_title_inve
                ];
            }
        }

        // Devolver los resultados en formato JSON
        return response()->json($result);
    }


    // Función para asignar permisos a los jurados en el documento de Google Drive
    protected function assignJuriesPermissions($documentId, $juradoEmails)
    {
        foreach ($juradoEmails as $email) {
            $this->googleDocumentController->assignDrivePermissions($documentId, $email, 'commenter');
        }
    }

    public function updateSoliciteJuriesForTesis(Request $request, $docof_id)
    {
        // Obtener el registro correspondiente en la base de datos
        $docof = DocOf::where('_id', $docof_id)->where('of_status', '!=' ,'tramitado')->first();

        if (!$docof) {
            return response()->json(['error' => 'Oficio no encontrado'], 404);
        }
        
        if ($docof->of_name != 'Solicitud de jurados para revision de tesis') {
            $tipo = 'informe';
            $name_doc = 'informe_link';
        }
        else{
            $tipo = 'tesis';
            $name_doc = 'document_link';
        }

        $state = $request->input('estado');

        $rules = [
            'estado' => 'required|string|in:observado,tramitado',
        ];
        // Manejo de diferentes estados usando un switch
        switch ($state) {
            case 'observado':
                $rules['observacion'] = 'required|string'; // Agrega la regla para rev_num_of
                $this->validate($request, ['observacion' => $rules['observacion']]);
                
                $docof->update([
                    'of_status' => $request->input('estado'),
                    'of_observation' => $request->input('observacion')
                ]);
                return response()->json([
                    'message' => 'Observacion enviada y actualizada',
                    'estado' => $docof->of_status,
                ], 200);

                break;

            case 'tramitado':

                $rules = [
                    'numero_oficio' => 'required|string',
                    'expediente' => 'required|string',
                    'presidente' => 'required|string',
                    'secretario' => 'required|string',
                    'vocal' => 'required|string',
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'errors' => $validator->errors()
                    ], 400);
                }
                
                $this->validate($request, $rules);

                $presidente =  Review::create([
                    'adviser_id' => $request->input('presidente'),
                    'student_id' => $docof->student_id,
                    'rev_num_of' => null,
                    'rev_count' => 0,
                    'rev_status' => 'pendiente', // Estado
                    'rev_type' => $tipo,
                    'rev_adviser_rol' => 'presidente', // asesor, presidente, secretario, vocal
                ]);

                $secretario =  Review::create([
                    'adviser_id' => $request->input('secretario'),
                    'student_id' => $docof->student_id,
                    'rev_num_of' => null, 
                    'rev_count' => 0,
                    'rev_status' => 'pendiente', // Estado
                    'rev_type' => $tipo,
                    'rev_adviser_rol' => 'secretario', // asesor, presidente, secretario, vocal
                ]);

                $vocal =  Review::create([
                    'adviser_id' => $request->input('vocal'),
                    'student_id' => $docof->student_id,
                    'rev_num_of' => null, 
                    'rev_count' => 0,
                    'rev_status' => 'pendiente', // Estado
                    'rev_type' => $tipo,
                    'rev_adviser_rol' => 'vocal', // asesor, presidente, secretario, vocal
                ]);

                if($tipo=='informe')
                {
                    $docResolution = new DocResolution([
                        'docof_id' => $docof->_id,
                        'docres_name' => $docof->of_name,  // Inicializado como null
                        'docres_num_res' => null,  // Inicializado como null
                        'docres_status' => 'pendiente',  // Estado fijo en "pendiente"
                        'docres_observation' => null  // Inicializado como null
                    ]);
                    $docResolution->save();
                }

                $presidente->save();
                $secretario->save();
                $vocal->save();
                
                $docof->update([
                    'of_status' => $request->input('estado'),
                    'of_num_of' => $request->input('numero_oficio'),
                    'of_num_exp' => $request->input('expediente'),
                    'of_observation' => null,
                ]);

                // Obtener el enlace del documento desde la tabla solicitude
                $solicitude = Solicitude::where('student_id', $docof->student_id)
                                        ->where('sol_status', 'aceptado')
                                        ->first();
                                        
                if ($solicitude && $solicitude->$name_doc) {
                    preg_match('/[-\w]{25,}/', $solicitude->$name_doc, $matches);
                    $documentId = $matches[0] ?? null;

                    if ($documentId) {
                        // Obtener correos electrónicos de los jurados
                        $juradoEmails = [];
                        $roles = ['presidente', 'secretario', 'vocal'];
                        foreach ($roles as $rol) {
                            $adviser = Adviser::find($request->input($rol));
                            if ($adviser) {
                                $user = User::find($adviser->user_id);
                                if ($user) {
                                    $juradoEmails[] = $user->email;
                                } else {
                                    return response()->json(['error' => "El usuario con rol $rol no fue encontrado"], 404);
                                }
                            } else {
                                return response()->json(['error' => "El asesor con rol $rol no fue encontrado"], 404);
                            }
                        }

                        // Asignar permisos de comentar a los jurados en el documento
                        foreach ($juradoEmails as $email) {
                            $this->googleDocumentController->assignDrivePermissions($documentId, $email, 'commenter');
                        }
                    } else {
                        return response()->json(['error' => 'Document ID no encontrado en el enlace'], 404);
                    }
                } else {
                    return response()->json(['error' => 'Solicitud o enlace de documento no encontrado'], 404);
                }

                return response()->json([
                    'message' => 'Oficio tramitado',
                    'estado' => $docof->of_status,
                ], 200);
               
                break;

            default:
                // Manejo de estado no reconocido
                return response()->json(['error' => 'Estado no válido'], 400);
    }

    $docof->save();
    return response()->json([
        'message' => 'Estado actualizado correctamente',
        'oficio_datos' => $docof,
    ], 200);
    }

    public function viewOfficeJuriesForTesis($docof_id){
        
        $office = DocOf::where('_id', $docof_id)->first();

        $num_exp = $office->of_num_exp;
    
        // Verifica si el registro no se encuentra
        if (!$office) {
            return redirect()->back()->with('error', 'Solicitud no encontrada');
        }
        
        // Formatear la fecha updated_at como "11 de julio de 2024"
        $formattedDate = Carbon::parse($office->updated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
        $year = Carbon::parse($office->updated_at)->locale('es')->isoFormat('YYYY');

        $revision_presidente = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'presidente')->first();
        $revision_secretario = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'secretario')->first();
        $revision_vocal = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'vocal')->first();


    
        // Recibe el id del Asesor
        $presidente = Adviser::where('_id', $revision_presidente->adviser_id)->first();
        $secretario = Adviser::where('_id', $revision_secretario->adviser_id)->first();
        $vocal = Adviser::where('_id', $revision_vocal->adviser_id)->first();


        $presidente = ucwords(strtolower($presidente->adv_name . ' ' . $presidente->adv_lastname_m . ' ' . $presidente->adv_lastname_f));
        $presidente = 'Ing. ' . $presidente . ' - ' . strtoupper($revision_presidente->rev_adviser_rol);
        
        $secretario = ucwords(strtolower($secretario->adv_name . ' ' . $secretario->adv_lastname_m . ' ' . $secretario->adv_lastname_f));
        $secretario = 'Ing. ' . $secretario . ' - ' . strtoupper($revision_secretario->rev_adviser_rol);

        $vocal = ucwords(strtolower($vocal->adv_name . ' ' . $vocal->adv_lastname_m . ' ' . $vocal->adv_lastname_f));
        $vocal = 'Ing. ' . $vocal . ' - ' . strtoupper($revision_vocal->rev_adviser_rol);
        

        $student = Student::where('_id', $office->student_id)->first();
        $student = strtoupper($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name);
            
        $tittle = Solicitude::where('student_id', $office->student_id)->first();
        $tittle = mb_strtoupper($tittle->sol_title_inve, 'UTF-8');

        $resolucion = Solicitude::where('student_id', $office->student_id)->first();
        $resolucion = DocOf::where('solicitude_id', $resolucion->_id)->first();
        $resolucion = DocResolution::where('docof_id', $resolucion->_id)->first();

        $num_res = $resolucion->docres_num_res;
        $res_date = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('DD[/]MM[/]YYYY');
        $res_year = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('YYYY');
            
        // Pasar los datos a la vista
        $pdf = Pdf::loadView('of_dj_pt', compact('office', 'tittle', 'formattedDate', 'presidente', 'secretario', 'vocal', 'student', 'year', 'num_exp', 'num_res', 'res_date', 'res_year'));
    
        return $pdf->stream(); // Puedes especificar un nombre para el archivo PDF
    }

    public function downloadOfficeJuriesForTesis($docof_id){
        
        $office = DocOf::where('_id', $docof_id)->first();

        $num_exp = $office->of_num_exp;
    
        // Verifica si el registro no se encuentra
        if (!$office) {
            return redirect()->back()->with('error', 'Solicitud no encontrada');
        }
        
        // Formatear la fecha updated_at como "11 de julio de 2024"
        $formattedDate = Carbon::parse($office->updated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
        $year = Carbon::parse($office->updated_at)->locale('es')->isoFormat('YYYY');

        $revision_presidente = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'presidente')->first();
        $revision_secretario = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'secretario')->first();
        $revision_vocal = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'vocal')->first();


    
        // Recibe el id del Asesor
        $presidente = Adviser::where('_id', $revision_presidente->adviser_id)->first();
        $secretario = Adviser::where('_id', $revision_secretario->adviser_id)->first();
        $vocal = Adviser::where('_id', $revision_vocal->adviser_id)->first();


        $presidente = ucwords(strtolower($presidente->adv_name . ' ' . $presidente->adv_lastname_m . ' ' . $presidente->adv_lastname_f));
        $presidente = 'Ing. ' . $presidente . ' - ' . strtoupper($revision_presidente->rev_adviser_rol);
        
        $secretario = ucwords(strtolower($secretario->adv_name . ' ' . $secretario->adv_lastname_m . ' ' . $secretario->adv_lastname_f));
        $secretario = 'Ing. ' . $secretario . ' - ' . strtoupper($revision_secretario->rev_adviser_rol);

        $vocal = ucwords(strtolower($vocal->adv_name . ' ' . $vocal->adv_lastname_m . ' ' . $vocal->adv_lastname_f));
        $vocal = 'Ing. ' . $vocal . ' - ' . strtoupper($revision_vocal->rev_adviser_rol);
        

        $student = Student::where('_id', $office->student_id)->first();
        $student = strtoupper($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name);
            
        $tittle = Solicitude::where('student_id', $office->student_id)->first();
        $tittle = mb_strtoupper($tittle->sol_title_inve, 'UTF-8');

        $resolucion = Solicitude::where('student_id', $office->student_id)->first();
        $resolucion = DocOf::where('solicitude_id', $resolucion->_id)->first();
        $resolucion = DocResolution::where('docof_id', $resolucion->_id)->first();

        $num_res = $resolucion->docres_num_res;
        $res_date = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('DD[/]MM[/]YYYY');
        $res_year = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('YYYY');
            
        // Pasar los datos a la vista
        $pdf = Pdf::loadView('of_dj_pt', compact('office', 'tittle', 'formattedDate', 'presidente', 'secretario', 'vocal', 'student', 'year', 'num_exp', 'num_res', 'res_date', 'res_year'));
        return $pdf->download('OFF-DJ-' . $student . '.pdf'); // Puedes especificar un nombre para el archivo PDF
    }

    public function soliciteOfficeApproveThesis($student_id)
    {
        // Verificar si el estudiante existe
        $student = Student::where('_id', $student_id)->first();
        if (!$student) {
            return response()->json([
                'mensaje' => 'El estudiante no existe',
            ], 400);
        }

        // Verificar si el estudiante tiene todos los jurados aprobados
        $presidente = Review::where('student_id', $student_id)
            ->where('rev_adviser_rol', 'presidente')
            ->where('rev_status', 'aprobado')
            ->first();
        $secretario = Review::where('student_id', $student_id)
            ->where('rev_adviser_rol', 'secretario')
            ->where('rev_status', 'aprobado')
            ->first();
        $vocal = Review::where('student_id', $student_id)
            ->where('rev_adviser_rol', 'vocal')
            ->where('rev_status', 'aprobado')
            ->first();

        if (!$presidente || !$secretario || !$vocal) {
            return response()->json([
                'mensaje' => 'El estudiante aún no tiene la conformidad de sus jurados',
            ], 400);
        }

        // Verificar si ya existe una solicitud de "Aprobación de tesis" pendiente
        $existingOffice = DocOf::where('student_id', $student_id)
            ->where('of_name', 'Aprobación de tesis')
            ->where('of_status', 'pendiente') // Añadimos condición de estado "pendiente"
            ->first();

        if ($existingOffice) {
            return response()->json([
                'mensaje' => 'El estudiante ya tiene una solicitud de aprobación de tesis pendiente',
            ], 400);
        }

        // Crear nueva solicitud de aprobación de tesis
        $office = DocOf::create([
            'student_id' => $student_id,
            'of_name' => 'Aprobación de tesis',
            'of_num_of' => null,
            'of_num_exp' => null,
            'of_status' => 'pendiente',
            'of_observation' => null,
        ]);

        return response()->json([
            'mensaje' => 'Aprobación de tesis creada correctamente',
            'estado' => $office->of_status,
        ], 200);
    }

    public function getOfficeApproveThesis() {

        // Obtener todas las solicitudes con el nombre 'Aprobación de tesis'
        $solicitude_docof = DocOf::where('of_name', 'Aprobación de tesis')->get();

        // Definir el orden deseado
        $order = ['pendiente', 'observado', 'tramitado'];

        // Ordenar manualmente las solicitudes por 'of_status'
        $sortedSolicitudes = $solicitude_docof->sort(function ($a, $b) use ($order) {
            return array_search($a->of_status, $order) <=> array_search($b->of_status, $order);
        })->values(); // Para asegurar que se mantenga como una colección indexada.

        // Crear un array para almacenar los resultados finales
        $result = [];

        // Recorrer cada solicitud ordenada y obtener los datos del estudiante y de la solicitud
        foreach ($sortedSolicitudes as $solicitude) {
            // Obtener el estudiante relacionado
            $student = Student::find($solicitude->student_id);
            // Obtener la solicitud relacionada
            $tittle = Solicitude::where('student_id', $solicitude->student_id)->first();
            
            $asesor = Review::where('student_id', $student->_id)
                                ->where('rev_type', 'tesis')
                                ->where('rev_adviser_rol', 'asesor')
                                ->first();
            $presidente = Review::where('student_id', $student->_id)
                                ->where('rev_type', 'tesis')
                                ->where('rev_adviser_rol', 'presidente')
                                ->first();
            $secretario = Review::where('student_id', $student->_id)
                                ->where('rev_type', 'tesis')
                                ->where('rev_adviser_rol', 'secretario')
                                ->first();
            $vocal = Review::where('student_id', $student->_id)
                                ->where('rev_type', 'tesis')
                                ->where('rev_adviser_rol', 'vocal')
                                ->first();            
            // Si los datos existen, agregar al resultado
            if ($student && $tittle) {
                $result[] = [
                    'oficio_id' => $solicitude->_id,
                    'nombre' => ucwords(strtolower($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name)),
                    'titulo' => $tittle->sol_title_inve,
                    'revision_id_asesor' => $asesor->_id,
                    'revision_id_presidente' => $presidente->_id,
                    'revision_id_secretario' => $secretario->_id,
                    'revision_id_vocal' => $vocal->_id,
                    'estado' => $solicitude->of_status,
                ];
            }
        }

        // Devolver los resultados en formato JSON
        return response()->json($result);

        }
        public function updateStatusOfficeApproveThesis(Request $request, $docof_id)
        {
            // Obtener el registro correspondiente en la base de datos
            $docof = DocOf::where('_id', $docof_id)->first();

            if (!$docof) {
                return response()->json(['error' => 'Oficio no encontrado'], 404);
            }
            
            $docres = DocResolution::where('docof_id', $docof_id)->first();

            if ($docres){
                return response()->json(['error' => 'Oficio ya aprobado'], 404);
            }

            $state = $request->input('estado');

            $rules = [
                'estado' => 'required|string|in:observado,tramitado',
            ];
            // Manejo de diferentes estados usando un switch
            switch ($state) {
                case 'observado':
                    $rules['observacion'] = 'required|string'; // Agrega la regla para rev_num_of
                    $this->validate($request, ['observacion' => $rules['observacion']]);
                    
                    $docof->update([
                        'of_status' => $request->input('estado'),
                        'of_observation' => $request->input('observacion')
                    ]);
                    return response()->json([
                        'message' => 'Observacion enviada y actualizada',
                        'observacion' => $docof->of_observation,
                        'estado' => $docof->of_status,
                    ], 200);

                    break;

                case 'tramitado':

                    $rules = [
                        'numero_oficio' => 'required|string',
                        'expediente' => 'required|string',
                    ];

                    $validator = Validator::make($request->all(), $rules);

                    if ($validator->fails()) {
                        return response()->json([
                            'status' => false,
                            'errors' => $validator->errors()
                        ], 400);
                    }
                    if ($docof->of_name == 'designacion de fecha y hora'){

                        $rules = [
                            'fecha' => 'required|string',
                            'hora' => 'required|string',
                            'accesitario_id' => 'required|string',
                        ];

                        $validator = Validator::make($request->all(), $rules);

                        $rev_pres_inf = Review::where('student_id', $docof->student_id)
                                        ->where('rev_type', 'informe')
                                        ->where('rev_adviser_rol', 'presidente')
                                        ->first();
                        $rev_secr_inf = Review::where('student_id', $docof->student_id)
                                        ->where('rev_type', 'informe')
                                        ->where('rev_adviser_rol', 'secretario')
                                        ->first();
                        $rev_voca_inf = Review::where('student_id', $docof->student_id)
                                        ->where('rev_type', 'informe')
                                        ->where('rev_adviser_rol', 'vocal')
                                        ->first();

                        $rev_pres_sus =  Review::create([
                                'student_id' => $docof->student_id,
                                'adviser_id' => $rev_pres_inf->adviser_id,
                                'rev_count' => 0,
                                'rev_status' => 'pendiente',
                                'rev_type' => 'sustentacion',
                                'rev_adviser_rol' => $rev_pres_inf->rev_adviser_rol,
                            ]);
                        $rev_secr_sus =  Review::create([
                                'student_id' => $docof->student_id,
                                'adviser_id' => $rev_secr_inf->adviser_id,
                                'rev_count' => 0,
                                'rev_status' => 'pendiente',
                                'rev_type' => 'sustentacion',
                                'rev_adviser_rol' => $rev_secr_inf->rev_adviser_rol,
                            ]);
                        $rev_voca_sus =  Review::create([
                                'student_id' => $docof->student_id,
                                'adviser_id' => $rev_voca_inf->adviser_id,
                                'rev_count' => 0,
                                'rev_status' => 'pendiente',
                                'rev_type' => 'sustentacion',
                                'rev_adviser_rol' => $rev_voca_inf->rev_adviser_rol,
                            ]);
                        $rev_acce_sus =  Review::create([
                                'student_id' => $docof->student_id,
                                'adviser_id' => $request->input('accesitario_id'),
                                'rev_count' => 0,
                                'rev_status' => 'pendiente',
                                'rev_type' => 'sustentacion',
                                'rev_adviser_rol' => 'accesitario',
                            ]);
                        $defense = Defense::create([
                            'student_id' => $docof->student_id,
                            'def_fecha' => $request->input('fecha'),
                            'def_hora' => $request->input('hora'),
                            'def_status' => 'pendiente',
                        ]);

                    };

                    $this->validate($request, $rules);

                    $docres =  DocResolution::create([
                        'docof_id' => $docof_id,
                        'docres_name' => $docof->of_name,
                        'docres_num_res' => null,
                        'docres_status' => 'pendiente',
                    ]);
                    
                    $docof->update([
                        'of_status' => $request->input('estado'),
                        'of_num_of' => $request->input('numero_oficio'),
                        'of_num_exp' => $request->input('expediente'),
                        'of_observation' => null,
                    ]);

                    return response()->json([
                        'message' => 'Oficio trámitado',
                        'estado' => $docof->of_status,
                    ], 200);
                
                    break;

                default:
                    // Manejo de estado no reconocido
                    return response()->json(['error' => 'Estado no válido'], 400);
        }

        $docof->save();
        return response()->json([
            'message' => 'Estado actualizado correctamente',
            'oficio_datos' => $docof,
        ], 200);
        }

        public function viewOfficeApproveThesis($docof_id){
        
            $office = DocOf::where('_id', $docof_id)->first();

            // Verifica si el registro no se encuentra
            if (!$office) {
                return redirect()->back()->with('error', 'Solicitud no encontrada');
            }

            $num_exp = $office->of_num_exp;
            // Formatear la fecha updated_at como "11 de julio de 2024"
            $formattedDate = Carbon::parse($office->updated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
            $year = Carbon::parse($office->updated_at)->locale('es')->isoFormat('YYYY');
            
            $student = Student::where('_id', $office->student_id)->first();
            $student = strtoupper($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name);

            $revision_presidente = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'presidente')->where('rev_type', 'tesis')->first();
            $revision_secretario = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'secretario')->where('rev_type', 'tesis')->first();
            $revision_vocal = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'vocal')->where('rev_type', 'tesis')->first();
            $revision_asesor = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'asesor')->where('rev_type', 'tesis')->first();
    
    
        
            // Recibe el id del Asesor
            $presidente = Adviser::where('_id', $revision_presidente->adviser_id)->first();
            $secretario = Adviser::where('_id', $revision_secretario->adviser_id)->first();
            $vocal = Adviser::where('_id', $revision_vocal->adviser_id)->first();
            $asesor = Adviser::where('_id', $revision_asesor->adviser_id)->first();
    
    
            $presidente = ucwords(strtolower($presidente->adv_name . ' ' . $presidente->adv_lastname_m . ' ' . $presidente->adv_lastname_f));
            $presidente = 'Ing. ' . $presidente . ' - ' . strtoupper($revision_presidente->rev_adviser_rol);
            
            $secretario = ucwords(strtolower($secretario->adv_name . ' ' . $secretario->adv_lastname_m . ' ' . $secretario->adv_lastname_f));
            $secretario = 'Ing. ' . $secretario . ' - ' . strtoupper($revision_secretario->rev_adviser_rol);
    
            $vocal = ucwords(strtolower($vocal->adv_name . ' ' . $vocal->adv_lastname_m . ' ' . $vocal->adv_lastname_f));
            $vocal = 'Ing. ' . $vocal . ' - ' . strtoupper($revision_vocal->rev_adviser_rol);

            $asesor = ucwords(strtolower($asesor->adv_name . ' ' . $asesor->adv_lastname_m . ' ' . $asesor->adv_lastname_f));
            $asesor = 'Ing. ' . $asesor;
                   
            $tittle = Solicitude::where('student_id', $office->student_id)->first();
            $tittle = mb_strtoupper($tittle->sol_title_inve, 'UTF-8');
    

            $resolucion = Solicitude::where('student_id', $office->student_id)->first();
            $resolucion = DocOf::where('solicitude_id', $resolucion->_id)->first();
            $resolucion = DocResolution::where('docof_id', $resolucion->_id)->first();
    
            $num_res = $resolucion->docres_num_res;
            $res_date = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('DD [de] MMMM [de] YYYY');
            $res_year = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('YYYY');
                
            // Pasar los datos a la vista
            $pdf = Pdf::loadView('of_apt', compact('asesor', 'office', 'tittle', 'formattedDate', 'presidente', 'secretario', 'vocal', 'student', 'year', 'num_exp', 'num_res', 'res_date', 'res_year'));
            return $pdf->stream(); // Puedes especificar un nombre para el archivo PDF
        }

        public function downloadOfficeApproveThesis($docof_id){
        
            $office = DocOf::where('_id', $docof_id)->first();

            // Verifica si el registro no se encuentra
            if (!$office) {
                return redirect()->back()->with('error', 'Solicitud no encontrada');
            }

            $num_exp = $office->of_num_exp;
            // Formatear la fecha updated_at como "11 de julio de 2024"
            $formattedDate = Carbon::parse($office->updated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
            $year = Carbon::parse($office->updated_at)->locale('es')->isoFormat('YYYY');
            
            $student = Student::where('_id', $office->student_id)->first();
            $student = strtoupper($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name);

            $revision_presidente = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'presidente')->where('rev_type', 'tesis')->first();
            $revision_secretario = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'secretario')->where('rev_type', 'tesis')->first();
            $revision_vocal = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'vocal')->where('rev_type', 'tesis')->first();
            $revision_asesor = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'asesor')->where('rev_type', 'tesis')->first();
    
    
        
            // Recibe el id del Asesor
            $presidente = Adviser::where('_id', $revision_presidente->adviser_id)->first();
            $secretario = Adviser::where('_id', $revision_secretario->adviser_id)->first();
            $vocal = Adviser::where('_id', $revision_vocal->adviser_id)->first();
            $asesor = Adviser::where('_id', $revision_asesor->adviser_id)->first();
    
    
            $presidente = ucwords(strtolower($presidente->adv_name . ' ' . $presidente->adv_lastname_m . ' ' . $presidente->adv_lastname_f));
            $presidente = 'Ing. ' . $presidente . ' - ' . strtoupper($revision_presidente->rev_adviser_rol);
            
            $secretario = ucwords(strtolower($secretario->adv_name . ' ' . $secretario->adv_lastname_m . ' ' . $secretario->adv_lastname_f));
            $secretario = 'Ing. ' . $secretario . ' - ' . strtoupper($revision_secretario->rev_adviser_rol);
    
            $vocal = ucwords(strtolower($vocal->adv_name . ' ' . $vocal->adv_lastname_m . ' ' . $vocal->adv_lastname_f));
            $vocal = 'Ing. ' . $vocal . ' - ' . strtoupper($revision_vocal->rev_adviser_rol);

            $asesor = ucwords(strtolower($asesor->adv_name . ' ' . $asesor->adv_lastname_m . ' ' . $asesor->adv_lastname_f));
            $asesor = 'Ing. ' . $asesor;
                   
            $tittle = Solicitude::where('student_id', $office->student_id)->first();
            $tittle = mb_strtoupper($tittle->sol_title_inve, 'UTF-8');
    

            $resolucion = Solicitude::where('student_id', $office->student_id)->first();
            $resolucion = DocOf::where('solicitude_id', $resolucion->_id)->first();
            $resolucion = DocResolution::where('docof_id', $resolucion->_id)->first();
    
            $num_res = $resolucion->docres_num_res;
            $res_date = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('DD [de] MMMM [de] YYYY');
            $res_year = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('YYYY');
                
            // Pasar los datos a la vista
            $pdf = Pdf::loadView('of_apt', compact('asesor', 'office', 'tittle', 'formattedDate', 'presidente', 'secretario', 'vocal', 'student', 'year', 'num_exp', 'num_res', 'res_date', 'res_year'));
            return $pdf->download('OFF-APT-' . $student . '.pdf'); // Puedes especificar un nombre para el archivo PDF
        }

        public function createOfficeJuriesForInforme($student_id)
        {
            $conf_adviser = Review::where('student_id', $student_id)
                            ->where('rev_adviser_rol', 'asesor')
                            ->where('rev_type', 'informe')
                            ->first();

            $search_docOf = DocOf::where('student_id', $student_id)
                            ->where('of_name', 'designacion de jurados para revision de informe final')
                            ->first();

            // Verificar si existe el asesor y si su estado es aprobado
            if ($conf_adviser == null || $conf_adviser->rev_status != 'aprobado') {
                return response()->json([
                    'estado' => 'no iniciado',
                    'message' => 'Este estudiante aun no tiene su conformidad por el asesor.'
                ], 404);
            }

            // Verificar si ya existe una solicitud de jurados
            if ($search_docOf != null && $search_docOf->of_name == 'designacion de jurados para revision de informe final') {
                return response()->json([
                    'estado' => 'pendiente',
                    'message' => 'Este estudiante ya tiene una solicitud en proceso.'
                ], 404);
            }

            // Crear una nueva solicitud
            $docOf = new DocOf([
                'student_id' => $student_id,
                'of_name' => 'designacion de jurados para revision de informe final',  
                'of_num_of' => null,  
                'of_num_exp' => null,  
                'of_status' => 'pendiente',  
                'of_observation' => null,  
            ]);

            // Guardar el nuevo documento en la base de datos
            $docOf->save();

            return response()->json([
                'estado' => $docOf->of_status,
                'status' => true,
                'message' => 'Solicitud enviada correctamente',
            ], 200);
        }

        public function getOfficeForJuriesInforme(){
            // Obtener todas las solicitudes con el nombre 'designacion de jurados para revision de informe final'
            $solicitude_docof = DocOf::where('of_name', 'designacion de jurados para revision de informe final')->get();

            // Definir el orden deseado
            $order = ['pendiente', 'observado', 'tramitado'];

            // Ordenar manualmente las solicitudes por 'of_status'
            $sortedSolicitudes = $solicitude_docof->sort(function ($a, $b) use ($order) {
                return array_search($a->of_status, $order) <=> array_search($b->of_status, $order);
            })->values(); // Para asegurar que se mantenga como una colección indexada.

            // Crear un array para almacenar los resultados finales
            $result = [];

            // Recorrer cada solicitud ordenada y obtener los datos del estudiante y de la solicitud
            foreach ($sortedSolicitudes as $solicitude) {
                // Obtener el estudiante relacionado
                $student = Student::find($solicitude->student_id);
                // Obtener la solicitud relacionada
                $tittle = Solicitude::where('student_id', $solicitude->student_id)->first();
                
                $asesor = Review::where('student_id', $student->_id)
                                    ->where('rev_type', 'informe')
                                    ->where('rev_adviser_rol', 'asesor')
                                    ->first();           
                // Si los datos existen, agregar al resultado
                if ($student && $tittle && $asesor->rev_status == 'aprobado') {
                    $result[] = [
                        'oficio_id' => $solicitude->_id,
                        'nombre' => ucwords(strtolower($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name)),
                        'titulo' => $tittle->sol_title_inve,
                        'revision_id_asesor' => $asesor->_id,
                        'estado' => $solicitude->of_status,
                    ];
                }
            }
            return response()->json([
                $result,
            ], 200);
        }

        public function viewOfficeJuriesForInforme($docof_id){
        
            $office = DocOf::where('_id', $docof_id)->first();
    
            $num_exp = $office->of_num_exp;
        
            // Verifica si el registro no se encuentra
            if (!$office) {
                return redirect()->back()->with('error', 'Solicitud no encontrada');
            }
            
            // Formatear la fecha updated_at como "11 de julio de 2024"
            $formattedDate = Carbon::parse($office->updated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
            $year = Carbon::parse($office->updated_at)->locale('es')->isoFormat('YYYY');
    
            $revision_presidente = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'presidente')->where('rev_type', 'informe')->first();
            $revision_secretario = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'secretario')->where('rev_type', 'informe')->first();
            $revision_vocal = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'vocal')->where('rev_type', 'informe')->first();   
        
            // Recibe el id del Asesor
            $presidente = Adviser::where('_id', $revision_presidente->adviser_id)->first();
            $secretario = Adviser::where('_id', $revision_secretario->adviser_id)->first();
            $vocal = Adviser::where('_id', $revision_vocal->adviser_id)->first();
    
    
            $presidente = ucwords(strtolower($presidente->adv_name . ' ' . $presidente->adv_lastname_m . ' ' . $presidente->adv_lastname_f));
            $presidente = 'Ing. ' . $presidente . ' - ' . strtoupper($revision_presidente->rev_adviser_rol);
            
            $secretario = ucwords(strtolower($secretario->adv_name . ' ' . $secretario->adv_lastname_m . ' ' . $secretario->adv_lastname_f));
            $secretario = 'Ing. ' . $secretario . ' - ' . strtoupper($revision_secretario->rev_adviser_rol);
    
            $vocal = ucwords(strtolower($vocal->adv_name . ' ' . $vocal->adv_lastname_m . ' ' . $vocal->adv_lastname_f));
            $vocal = 'Ing. ' . $vocal . ' - ' . strtoupper($revision_vocal->rev_adviser_rol);
            
    
            $student = Student::where('_id', $office->student_id)->first();
            $student = strtoupper($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name);
                
            $tittle = Solicitude::where('student_id', $office->student_id)->first();
            $tittle = mb_strtoupper($tittle->sol_title_inve, 'UTF-8');
    
            $resolucion = DocOf::where('student_id', $office->student_id)->where('of_name', 'Aprobación de tesis')->first();
            $resolucion = DocResolution::where('docof_id', $resolucion->_id)->first();
    
            $num_res = $resolucion->docres_num_res;
            $res_date = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('DD[/]MM[/]YYYY');
            $res_year = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('YYYY');

                
            // Pasar los datos a la vista
            $pdf = Pdf::loadView('of_dj_if', compact('office', 'tittle', 'formattedDate', 'presidente', 'secretario', 'vocal', 'student', 'year', 'num_exp', 'num_res', 'res_date', 'res_year'));
        
            return $pdf->stream(); // Puedes especificar un nombre para el archivo PDF
        }

        public function downloadOfficeJuriesForInforme($docof_id){
        
            $office = DocOf::where('_id', $docof_id)->first();
    
            $num_exp = $office->of_num_exp;
        
            // Verifica si el registro no se encuentra
            if (!$office) {
                return redirect()->back()->with('error', 'Solicitud no encontrada');
            }
            
            // Formatear la fecha updated_at como "11 de julio de 2024"
            $formattedDate = Carbon::parse($office->updated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
            $year = Carbon::parse($office->updated_at)->locale('es')->isoFormat('YYYY');
    
            $revision_presidente = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'presidente')->where('rev_type', 'informe')->first();
            $revision_secretario = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'secretario')->where('rev_type', 'informe')->first();
            $revision_vocal = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'vocal')->where('rev_type', 'informe')->first();   
        
            // Recibe el id del Asesor
            $presidente = Adviser::where('_id', $revision_presidente->adviser_id)->first();
            $secretario = Adviser::where('_id', $revision_secretario->adviser_id)->first();
            $vocal = Adviser::where('_id', $revision_vocal->adviser_id)->first();
    
    
            $presidente = ucwords(strtolower($presidente->adv_name . ' ' . $presidente->adv_lastname_m . ' ' . $presidente->adv_lastname_f));
            $presidente = 'Ing. ' . $presidente . ' - ' . strtoupper($revision_presidente->rev_adviser_rol);
            
            $secretario = ucwords(strtolower($secretario->adv_name . ' ' . $secretario->adv_lastname_m . ' ' . $secretario->adv_lastname_f));
            $secretario = 'Ing. ' . $secretario . ' - ' . strtoupper($revision_secretario->rev_adviser_rol);
    
            $vocal = ucwords(strtolower($vocal->adv_name . ' ' . $vocal->adv_lastname_m . ' ' . $vocal->adv_lastname_f));
            $vocal = 'Ing. ' . $vocal . ' - ' . strtoupper($revision_vocal->rev_adviser_rol);
            
    
            $student = Student::where('_id', $office->student_id)->first();
            $student = strtoupper($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name);
                
            $tittle = Solicitude::where('student_id', $office->student_id)->first();
            $tittle = mb_strtoupper($tittle->sol_title_inve, 'UTF-8');
    
            $resolucion = DocOf::where('student_id', $office->student_id)->where('of_name', 'Aprobación de tesis')->first();
            $resolucion = DocResolution::where('docof_id', $resolucion->_id)->first();
    
            $num_res = $resolucion->docres_num_res;
            $res_date = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('DD[/]MM[/]YYYY');
            $res_year = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('YYYY');

                
            // Pasar los datos a la vista
            $pdf = Pdf::loadView('of_dj_if', compact('office', 'tittle', 'formattedDate', 'presidente', 'secretario', 'vocal', 'student', 'year', 'num_exp', 'num_res', 'res_date', 'res_year'));
            return $pdf->download('OFF-DJIF-' . $student . '.pdf'); // Puedes especificar un nombre para el archivo PDF
        }

        public function soliciteOfficeApproveInforme($student_id)
    {

        // Verificar si el estudiante existe
        $student = Student::where('_id', $student_id)->first();
        if (!$student) {
            return response()->json([
                'mensaje' => 'El estudiante no existe',
            ], 400);
        }

        $filter = Filter::where('student_id', $student_id)
                ->where('fil_name', 'tercer filtro')
                ->where('fil_status', 'aprobado')
                ->first();
        if (!$filter) {
            return response()->json([
                'mensaje' => 'El estudiante no pasó turnitin',
            ], 400);
        }

        // Verificar si ya existe una solicitud de "Aprobación de tesis" pendiente
        $existingOffice = DocOf::where('student_id', $student_id)
            ->where('of_name', 'Aprobación de informe')
            ->first();

        if ($existingOffice) {
            return response()->json([
                'mensaje' => 'El estudiante ya tiene una solicitud de aprobación de informe final pendiente',
            ], 400);
        }

        // Crear nueva solicitud de aprobación de tesis
        $office = DocOf::create([
            'student_id' => $student_id,
            'of_name' => 'Aprobación de informe',
            'of_num_of' => null,
            'of_num_exp' => null,
            'of_status' => 'pendiente',
            'of_observation' => null,
        ]);

        // Guardar el nuevo documento en la base de datos
        return response()->json([
            'mensaje' => 'Aprobación de informe creada correctamente',
            'estado' => $office->of_status,
        ], 200);
    }

    public function getOfficeApproveInforme() {

        // Obtener todas las solicitudes con el nombre 'Aprobación de tesis'
        $solicitude_docof = DocOf::where('of_name', 'Aprobación de informe')->get();

        // Definir el orden deseado
        $order = ['pendiente', 'observado', 'tramitado'];

        // Ordenar manualmente las solicitudes por 'of_status'
        $sortedSolicitudes = $solicitude_docof->sort(function ($a, $b) use ($order) {
            return array_search($a->of_status, $order) <=> array_search($b->of_status, $order);
        })->values(); // Para asegurar que se mantenga como una colección indexada.

        // Crear un array para almacenar los resultados finales
        $result = [];

        // Recorrer cada solicitud ordenada y obtener los datos del estudiante y de la solicitud
        foreach ($sortedSolicitudes as $solicitude) {
            // Obtener el estudiante relacionado
            $student = Student::find($solicitude->student_id);
            // Obtener la solicitud relacionada
            $tittle = Solicitude::where('student_id', $solicitude->student_id)->first();
            
            $asesor = Review::where('student_id', $student->_id)
                                ->where('rev_type', 'informe')
                                ->where('rev_adviser_rol', 'asesor')
                                ->first();
            $presidente = Review::where('student_id', $student->_id)
                                ->where('rev_type', 'informe')
                                ->where('rev_adviser_rol', 'presidente')
                                ->first();
            $secretario = Review::where('student_id', $student->_id)
                                ->where('rev_type', 'informe')
                                ->where('rev_adviser_rol', 'secretario')
                                ->first();
            $vocal = Review::where('student_id', $student->_id)
                                ->where('rev_type', 'informe')
                                ->where('rev_adviser_rol', 'vocal')
                                ->first();            
            // Si los datos existen, agregar al resultado
            if ($student && $tittle) {
                $result[] = [
                    'oficio_id' => $solicitude->_id,
                    'nombre' => ucwords(strtolower($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name)),
                    'titulo' => $tittle->sol_title_inve,
                    'revision_id_asesor' => $asesor->_id,
                    'revision_id_presidente' => $presidente->_id,
                    'revision_id_secretario' => $secretario->_id,
                    'revision_id_vocal' => $vocal->_id,
                    'estado' => $solicitude->of_status,
                ];
            }
        }

        // Devolver los resultados en formato JSON
        return response()->json($result);

        }

        public function viewOfficeApproveInforme($docof_id){
        
            $office = DocOf::where('_id', $docof_id)->first();

            // Verifica si el registro no se encuentra
            if (!$office) {
                return redirect()->back()->with('error', 'Solicitud no encontrada');
            }

            $num_exp = $office->of_num_exp;
            // Formatear la fecha updated_at como "11 de julio de 2024"
            $formattedDate = Carbon::parse($office->updated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
            $year = Carbon::parse($office->updated_at)->locale('es')->isoFormat('YYYY');
            
            $student = Student::where('_id', $office->student_id)->first();
            $student = strtoupper($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name);

            $revision_presidente = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'presidente')->where('rev_type', 'informe')->first();
            $revision_secretario = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'secretario')->where('rev_type', 'informe')->first();
            $revision_vocal = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'vocal')->where('rev_type', 'informe')->first();
            $revision_asesor = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'asesor')->where('rev_type', 'informe')->first();
    
    
        
            // Recibe el id del Asesor
            $presidente = Adviser::where('_id', $revision_presidente->adviser_id)->first();
            $secretario = Adviser::where('_id', $revision_secretario->adviser_id)->first();
            $vocal = Adviser::where('_id', $revision_vocal->adviser_id)->first();
            $asesor = Adviser::where('_id', $revision_asesor->adviser_id)->first();
    
    
            $presidente = ucwords(strtolower($presidente->adv_name . ' ' . $presidente->adv_lastname_m . ' ' . $presidente->adv_lastname_f));
            $presidente = 'Ing. ' . $presidente . ' - ' . strtoupper($revision_presidente->rev_adviser_rol);
            
            $secretario = ucwords(strtolower($secretario->adv_name . ' ' . $secretario->adv_lastname_m . ' ' . $secretario->adv_lastname_f));
            $secretario = 'Ing. ' . $secretario . ' - ' . strtoupper($revision_secretario->rev_adviser_rol);
    
            $vocal = ucwords(strtolower($vocal->adv_name . ' ' . $vocal->adv_lastname_m . ' ' . $vocal->adv_lastname_f));
            $vocal = 'Ing. ' . $vocal . ' - ' . strtoupper($revision_vocal->rev_adviser_rol);

            $asesor = ucwords(strtolower($asesor->adv_name . ' ' . $asesor->adv_lastname_m . ' ' . $asesor->adv_lastname_f));
            $asesor = 'Ing. ' . $asesor;
                   
            $tittle = Solicitude::where('student_id', $office->student_id)->first();
            $tittle = mb_strtoupper($tittle->sol_title_inve, 'UTF-8');
    

            $resolucion = DocOf::where('student_id', $office->student_id)->where('of_name', 'Aprobación de tesis')->first();
            $resolucion = DocResolution::where('docof_id', $resolucion->_id)->first();
    
            $num_res = $resolucion->docres_num_res;
            $res_date = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('DD [de] MMMM [de] YYYY');
            $res_year = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('YYYY');
                
            // Pasar los datos a la vista
            $pdf = Pdf::loadView('of_aif', compact('asesor', 'office', 'tittle', 'formattedDate', 'presidente', 'secretario', 'vocal', 'student', 'year', 'num_exp', 'num_res', 'res_date', 'res_year'));
            return $pdf->stream(); // Puedes especificar un nombre para el archivo PDF
        }
        
        public function downloadOfficeApproveInforme($docof_id){
        
            $office = DocOf::where('_id', $docof_id)->first();

            // Verifica si el registro no se encuentra
            if (!$office) {
                return redirect()->back()->with('error', 'Solicitud no encontrada');
            }

            $num_exp = $office->of_num_exp;
            // Formatear la fecha updated_at como "11 de julio de 2024"
            $formattedDate = Carbon::parse($office->updated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
            $year = Carbon::parse($office->updated_at)->locale('es')->isoFormat('YYYY');
            
            $student = Student::where('_id', $office->student_id)->first();
            $student = strtoupper($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name);

            $revision_presidente = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'presidente')->where('rev_type', 'informe')->first();
            $revision_secretario = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'secretario')->where('rev_type', 'informe')->first();
            $revision_vocal = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'vocal')->where('rev_type', 'informe')->first();
            $revision_asesor = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'asesor')->where('rev_type', 'informe')->first();
    
    
        
            // Recibe el id del Asesor
            $presidente = Adviser::where('_id', $revision_presidente->adviser_id)->first();
            $secretario = Adviser::where('_id', $revision_secretario->adviser_id)->first();
            $vocal = Adviser::where('_id', $revision_vocal->adviser_id)->first();
            $asesor = Adviser::where('_id', $revision_asesor->adviser_id)->first();
    
    
            $presidente = ucwords(strtolower($presidente->adv_name . ' ' . $presidente->adv_lastname_m . ' ' . $presidente->adv_lastname_f));
            $presidente = 'Ing. ' . $presidente . ' - ' . strtoupper($revision_presidente->rev_adviser_rol);
            
            $secretario = ucwords(strtolower($secretario->adv_name . ' ' . $secretario->adv_lastname_m . ' ' . $secretario->adv_lastname_f));
            $secretario = 'Ing. ' . $secretario . ' - ' . strtoupper($revision_secretario->rev_adviser_rol);
    
            $vocal = ucwords(strtolower($vocal->adv_name . ' ' . $vocal->adv_lastname_m . ' ' . $vocal->adv_lastname_f));
            $vocal = 'Ing. ' . $vocal . ' - ' . strtoupper($revision_vocal->rev_adviser_rol);

            $asesor = ucwords(strtolower($asesor->adv_name . ' ' . $asesor->adv_lastname_m . ' ' . $asesor->adv_lastname_f));
            $asesor = 'Ing. ' . $asesor;
                   
            $tittle = Solicitude::where('student_id', $office->student_id)->first();
            $tittle = mb_strtoupper($tittle->sol_title_inve, 'UTF-8');
    

            $resolucion = DocOf::where('student_id', $office->student_id)->where('of_name', 'Aprobación de tesis')->first();
            $resolucion = DocResolution::where('docof_id', $resolucion->_id)->first();
    
            $num_res = $resolucion->docres_num_res;
            $res_date = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('DD [de] MMMM [de] YYYY');
            $res_year = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('YYYY');
                
            // Pasar los datos a la vista
            $pdf = Pdf::loadView('of_aif', compact('asesor', 'office', 'tittle', 'formattedDate', 'presidente', 'secretario', 'vocal', 'student', 'year', 'num_exp', 'num_res', 'res_date', 'res_year'));
            return $pdf->download('OFF-AIF-' . $student . '.pdf'); // Puedes especificar un nombre para el archivo PDF
        }

        public function soliciteOfficeDeclareApto($student_id)
        {
            $off = DocOf::where('student_id', $student_id)
                            ->where('of_name', 'Aprobación de informe')
                            ->first();            

            if(!$off){
                return response()->json([
                    'estado' => 'no iniciado',
                    'message' => 'Este estudiante no tiene aprobación de informe por la facultad.'
                ], 404);
            }

            $res = DocResolution::where('docof_id', $off->_id)
                            ->where('docres_name', 'Aprobación de informe')
                            ->where('docres_status', 'tramitado')
                            ->first();

            if(!$res){
                return response()->json([
                    'estado' => 'no iniciado',
                    'message' => 'Este estudiante no tiene aprobación de informe por la facultad.'
                ], 404);
            }
            
            $search = DocOf::where('student_id', $off->student_id)->where('of_name','declaracion como apto')->first();

            // Verificar si ya existe una solicitud de jurados
            if ($search) {
                return response()->json([
                    'estado' => 'pendiente',
                    'message' => 'Este estudiante ya tiene una solicitud en proceso.'
                ], 404);
            }

            
            // Crear una nueva solicitud
            $docOf = new DocOf([
                'student_id' => $student_id,
                'of_name' => 'declaracion como apto',  
                'of_num_of' => null,  
                'of_num_exp' => null,  
                'of_status' => 'pendiente',  
                'of_observation' => null,  
            ]);

            // Guardar el nuevo documento en la base de datos
            $docOf->save();

            return response()->json([
                'estado' => $docOf->of_status,
                'status' => true,
                'message' => 'Solicitud enviada correctamente',
            ], 200);
        }

        public function getOfficeDeclareApto(){
            // Obtener todas las solicitudes con el nombre 'declaracion como apto'
            $solicitude_docof = DocOf::where('of_name', 'declaracion como apto')->get();

            // Definir el orden deseado
            $order = ['pendiente', 'observado', 'tramitado'];

            // Ordenar manualmente las solicitudes por 'of_status'
            $sortedSolicitudes = $solicitude_docof->sort(function ($a, $b) use ($order) {
                return array_search($a->of_status, $order) <=> array_search($b->of_status, $order);
            })->values(); // Para asegurar que se mantenga como una colección indexada.

            // Crear un array para almacenar los resultados finales
            $result = [];

            // Recorrer cada solicitud ordenada y obtener los datos del estudiante y de la solicitud
            foreach ($sortedSolicitudes as $solicitude) {
                // Obtener el estudiante relacionado
                $student = Student::find($solicitude->student_id);
                // Obtener la solicitud relacionada
                $tittle = Solicitude::where('student_id', $solicitude->student_id)->first();
                
                // Si los datos existen, agregar al resultado
                if ($student && $tittle) {
                    $result[] = [
                        'oficio_id' => $solicitude->_id,
                        'nombre' => ucwords(strtolower($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name)),
                        'titulo' => $tittle->sol_title_inve,
                        'estado' => $solicitude->of_status,
                    ];
                }
            }
            return response()->json([
                $result,
            ], 200);
        }

        public function soliciteOfficeDesignationDate($student_id)
        {
            $requisito_off = DocOf::where('student_id', $student_id)
                                ->where('of_name', 'declaracion como apto')
                                ->where('of_status', 'tramitado')
                                ->first();            

            if(!$requisito_off){
                return response()->json([
                    'estado' => 'no iniciado',
                    'message' => 'Faltan requisitos.'
                ], 404);
            }

            $requisito_res = DocResolution::where('docof_id', $requisito_off->id)
                            ->where('docres_status', 'tramitado')
                            ->first();            

            if(!$requisito_res){
                return response()->json([
                    'estado' => 'no iniciado',
                    'message' => 'Este estudiante no fue declarado apto para la sustentación.'
                ], 404);
            }

            $off_exist = DocOf::where('student_id', $student_id)
                            ->where('of_name', 'designacion de fecha y hora')
                            ->first();

            if($off_exist){
                return response()->json([
                    'estado' => 'pendiente',
                    'message' => 'Este estudiante tiene una solicitud pendiente.'
                ], 404);
            }
            
            // Crear una nueva solicitud
            $docOf = new DocOf([
                'student_id' => $student_id,
                'of_name' => 'designacion de fecha y hora',  
                'of_num_of' => null,  
                'of_num_exp' => null,  
                'of_status' => 'pendiente',  
                'of_observation' => null,  
            ]);

            // Guardar el nuevo documento en la base de datos
            $docOf->save();

            return response()->json([
                'estado' => $docOf->of_status,
                'status' => true,
                'message' => 'Solicitud enviada correctamente',
            ], 200);
        }

        public function getOfficeDesignationDate(){
            // Obtener todas las solicitudes con el nombre 'designacion de fecha y hora'
            $solicitude_docof = DocOf::where('of_name', 'designacion de fecha y hora')->get();

            // Definir el orden deseado
            $order = ['pendiente', 'observado', 'tramitado'];

            // Ordenar manualmente las solicitudes por 'of_status'
            $sortedSolicitudes = $solicitude_docof->sort(function ($a, $b) use ($order) {
                return array_search($a->of_status, $order) <=> array_search($b->of_status, $order);
            })->values(); // Para asegurar que se mantenga como una colección indexada.

            // Crear un array para almacenar los resultados finales
            $result = [];

            // Recorrer cada solicitud ordenada y obtener los datos del estudiante y de la solicitud
            foreach ($sortedSolicitudes as $solicitude) {
                // Obtener el estudiante relacionado
                $student = Student::find($solicitude->student_id);
                // Obtener la solicitud relacionada
                $tittle = Solicitude::where('student_id', $solicitude->student_id)->first();
                
                // Si los datos existen, agregar al resultado
                if ($student && $tittle) {
                    $result[] = [
                        'oficio_id' => $solicitude->_id,
                        'nombre' => ucwords(strtolower($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name)),
                        'titulo' => $tittle->sol_title_inve,
                        'estado' => $solicitude->of_status,
                    ];
                }
            }
            return response()->json([
                $result,
            ], 200);
        }

        public function viewOfficeDeclareApto($docof_id){
        
            $office = DocOf::where('_id', $docof_id)->first();

            // Verifica si el registro no se encuentra
            if (!$office) {
                return redirect()->back()->with('error', 'Solicitud no encontrada');
            }

            $num_exp = $office->of_num_exp;
            // Formatear la fecha updated_at como "11 de julio de 2024"
            $formattedDate = Carbon::parse($office->updated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
            $year = Carbon::parse($office->updated_at)->locale('es')->isoFormat('YYYY');
            
            $student = Student::where('_id', $office->student_id)->first();
            $student = strtoupper($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name);
        
                   
            $tittle = Solicitude::where('student_id', $office->student_id)->first();
            $tittle = mb_strtoupper($tittle->sol_title_inve, 'UTF-8');
    

            $resolucion = DocOf::where('student_id', $office->student_id)->where('of_name', 'Aprobación de tesis')->first();
            $resolucion = DocResolution::where('docof_id', $resolucion->_id)->first();
    
            $num_res = $resolucion->docres_num_res;
            $res_date = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('DD [de] MMMM [de] YYYY');
            $res_year = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('YYYY');
                
            // Pasar los datos a la vista
            $pdf = Pdf::loadView('of_daps', compact('office', 'formattedDate','student', 'year', 'num_exp', 'num_res', 'res_date', 'res_year'));
            return $pdf->stream(); // Puedes especificar un nombre para el archivo PDF
        }

        public function downloadOfficeDeclareApto($docof_id){
        
            $office = DocOf::where('_id', $docof_id)->first();

            // Verifica si el registro no se encuentra
            if (!$office) {
                return redirect()->back()->with('error', 'Solicitud no encontrada');
            }

            $num_exp = $office->of_num_exp;
            // Formatear la fecha updated_at como "11 de julio de 2024"
            $formattedDate = Carbon::parse($office->updated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
            $year = Carbon::parse($office->updated_at)->locale('es')->isoFormat('YYYY');
            
            $student = Student::where('_id', $office->student_id)->first();
            $student = strtoupper($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name);
        
                   
            $tittle = Solicitude::where('student_id', $office->student_id)->first();
            $tittle = mb_strtoupper($tittle->sol_title_inve, 'UTF-8');
    

            $resolucion = DocOf::where('student_id', $office->student_id)->where('of_name', 'Aprobación de tesis')->first();
            $resolucion = DocResolution::where('docof_id', $resolucion->_id)->first();
    
            $num_res = $resolucion->docres_num_res;
            $res_date = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('DD [de] MMMM [de] YYYY');
            $res_year = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('YYYY');
                
            // Pasar los datos a la vista
            $pdf = Pdf::loadView('of_daps', compact('office', 'formattedDate','student', 'year', 'num_exp', 'num_res', 'res_date', 'res_year'));
            return $pdf->download('OFF-DAPS-' . $student . '.pdf'); // Puedes especificar un nombre para el archivo PDF
        }

        public function viewOfficeDesignationDate($docof_id){
        
            $office = DocOf::where('_id', $docof_id)->first();

            // Verifica si el registro no se encuentra
            if (!$office) {
                return redirect()->back()->with('error', 'Solicitud no encontrada');
            }

            $num_exp = $office->of_num_exp;
            // Formatear la fecha updated_at como "11 de julio de 2024"
            $formattedDate = Carbon::parse($office->updated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
            $year = Carbon::parse($office->updated_at)->locale('es')->isoFormat('YYYY');
            
            $student = Student::where('_id', $office->student_id)->first();
            $student = strtoupper($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name);

            $revision_presidente = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'presidente')->where('rev_type', 'sustentacion')->first();
            $revision_secretario = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'secretario')->where('rev_type', 'sustentacion')->first();
            $revision_vocal = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'vocal')->where('rev_type', 'sustentacion')->first();
            $revision_asesor = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'accesitario')->where('rev_type', 'sustentacion')->first();
    
    
        
            // Recibe el id del Asesor
            $presidente = Adviser::where('_id', $revision_presidente->adviser_id)->first();
            $secretario = Adviser::where('_id', $revision_secretario->adviser_id)->first();
            $vocal = Adviser::where('_id', $revision_vocal->adviser_id)->first();
            $asesor = Adviser::where('_id', $revision_asesor->adviser_id)->first();
    
    
            $presidente = ucwords(strtolower($presidente->adv_name . ' ' . $presidente->adv_lastname_m . ' ' . $presidente->adv_lastname_f));
            $presidente = 'Ing. ' . $presidente . ' - ' . strtoupper($revision_presidente->rev_adviser_rol);
            
            $secretario = ucwords(strtolower($secretario->adv_name . ' ' . $secretario->adv_lastname_m . ' ' . $secretario->adv_lastname_f));
            $secretario = 'Ing. ' . $secretario . ' - ' . strtoupper($revision_secretario->rev_adviser_rol);
    
            $vocal = ucwords(strtolower($vocal->adv_name . ' ' . $vocal->adv_lastname_m . ' ' . $vocal->adv_lastname_f));
            $vocal = 'Ing. ' . $vocal . ' - ' . strtoupper($revision_vocal->rev_adviser_rol);

            $asesor = ucwords(strtolower($asesor->adv_name . ' ' . $asesor->adv_lastname_m . ' ' . $asesor->adv_lastname_f));
            $asesor = 'Ing. ' . $asesor;
                   
            $tittle = Solicitude::where('student_id', $office->student_id)->first();
            $tittle = mb_strtoupper($tittle->sol_title_inve, 'UTF-8');
    

            $resolucion = DocOf::where('student_id', $office->student_id)->where('of_name', 'Aprobación de tesis')->first();
            $resolucion = DocResolution::where('docof_id', $resolucion->_id)->first();
    
            $num_res = $resolucion->docres_num_res;
            $res_date = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('DD [de] MMMM [de] YYYY');
            $res_year = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('YYYY');

            $def = Defense::where('student_id', $office->student_id)->first() ;

            $dateString = $def->def_fecha;
            $date = DateTime::createFromFormat('Y-m-d', $dateString);
            // Configura el idioma a español
            setlocale(LC_TIME, 'es_PE.UTF-8', 'es_ES.UTF-8', 'Spanish_Peru.1252', 'Spanish_Spain.1252');
            // Convierte la fecha al formato deseado
            $def_fecha = strtolower(strftime('%A %d de %B del %Y', $date->getTimestamp()));      
            $def_hora = strtolower($def->def_hora);
            // Pasar los datos a la vista
            $pdf = Pdf::loadView('of_dfh', compact('def_hora', 'def_fecha', 'asesor', 'office', 'tittle', 'formattedDate', 'presidente', 'secretario', 'vocal', 'student', 'year', 'num_exp', 'num_res', 'res_date', 'res_year'));
            return $pdf->stream(); // Puedes especificar un nombre para el archivo PDF
        }

        public function downloadOfficeDesignationDate($docof_id){
        
            $office = DocOf::where('_id', $docof_id)->first();

            // Verifica si el registro no se encuentra
            if (!$office) {
                return redirect()->back()->with('error', 'Solicitud no encontrada');
            }

            $num_exp = $office->of_num_exp;
            // Formatear la fecha updated_at como "11 de julio de 2024"
            $formattedDate = Carbon::parse($office->updated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
            $year = Carbon::parse($office->updated_at)->locale('es')->isoFormat('YYYY');
            
            $student = Student::where('_id', $office->student_id)->first();
            $student = strtoupper($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name);

            $revision_presidente = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'presidente')->where('rev_type', 'sustentacion')->first();
            $revision_secretario = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'secretario')->where('rev_type', 'sustentacion')->first();
            $revision_vocal = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'vocal')->where('rev_type', 'sustentacion')->first();
            $revision_asesor = Review::where('student_id', $office->student_id)->where('rev_adviser_rol', 'accesitario')->where('rev_type', 'sustentacion')->first();
    
    
        
            // Recibe el id del Asesor
            $presidente = Adviser::where('_id', $revision_presidente->adviser_id)->first();
            $secretario = Adviser::where('_id', $revision_secretario->adviser_id)->first();
            $vocal = Adviser::where('_id', $revision_vocal->adviser_id)->first();
            $asesor = Adviser::where('_id', $revision_asesor->adviser_id)->first();
    
    
            $presidente = ucwords(strtolower($presidente->adv_name . ' ' . $presidente->adv_lastname_m . ' ' . $presidente->adv_lastname_f));
            $presidente = 'Ing. ' . $presidente . ' - ' . strtoupper($revision_presidente->rev_adviser_rol);
            
            $secretario = ucwords(strtolower($secretario->adv_name . ' ' . $secretario->adv_lastname_m . ' ' . $secretario->adv_lastname_f));
            $secretario = 'Ing. ' . $secretario . ' - ' . strtoupper($revision_secretario->rev_adviser_rol);
    
            $vocal = ucwords(strtolower($vocal->adv_name . ' ' . $vocal->adv_lastname_m . ' ' . $vocal->adv_lastname_f));
            $vocal = 'Ing. ' . $vocal . ' - ' . strtoupper($revision_vocal->rev_adviser_rol);

            $asesor = ucwords(strtolower($asesor->adv_name . ' ' . $asesor->adv_lastname_m . ' ' . $asesor->adv_lastname_f));
            $asesor = 'Ing. ' . $asesor;
                   
            $tittle = Solicitude::where('student_id', $office->student_id)->first();
            $tittle = mb_strtoupper($tittle->sol_title_inve, 'UTF-8');
    

            $resolucion = DocOf::where('student_id', $office->student_id)->where('of_name', 'Aprobación de tesis')->first();
            $resolucion = DocResolution::where('docof_id', $resolucion->_id)->first();
    
            $num_res = $resolucion->docres_num_res;
            $res_date = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('DD [de] MMMM [de] YYYY');
            $res_year = Carbon::parse($resolucion->updated_at)->locale('es')->isoFormat('YYYY');

            $def = Defense::where('student_id', $office->student_id)->first() ;
            $dateString = '2024-11-10';
            $date = DateTime::createFromFormat('Y-m-d', $dateString);
            // Configura el idioma a español
            setlocale(LC_TIME, 'es_PE.UTF-8', 'es_ES.UTF-8', 'Spanish_Peru.1252', 'Spanish_Spain.1252');
            // Convierte la fecha al formato deseado
            $def_fecha = strtolower(strftime('%A %d de %B del %Y', $date->getTimestamp()));      
            $def_hora = strtolower($def->def_hora);
            // Pasar los datos a la vista
            $pdf = Pdf::loadView('of_dfh', compact('def_hora', 'def_fecha', 'asesor', 'office', 'tittle', 'formattedDate', 'presidente', 'secretario', 'vocal', 'student', 'year', 'num_exp', 'num_res', 'res_date', 'res_year'));
            return $pdf->download('OFF-DFH-' . $student . '.pdf'); // Puedes especificar un nombre para el archivo PDF
        }
    }
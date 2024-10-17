<?php

namespace App\Http\Controllers;

use App\Http\Resources\DocOfResource;
use App\Models\Adviser;
use App\Models\DocOf;
use App\Models\DocResolution;
use App\Models\History;
use App\Models\Review;
use App\Models\Solicitude;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocOfController extends Controller
{
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

    public function getOffices(){
        $docs = DocOf::where('of_status', 'tramitado')->where('of_name','Solicitud de resolución de designación de asesor')->get();
        return DocOfResource::collection($docs);
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


    public function updateSoliciteJuriesForTesis(Request $request, $docof_id)
    {
        // Obtener el registro correspondiente en la base de datos
        $docof = DocOf::where('_id', $docof_id)->first();

        if (!$docof) {
            return response()->json(['error' => 'Oficio no encontrado'], 404);
        }
        
        if ($docof->of_name != 'Solicitud de jurados para revision de tesis') {
            return response()->json(['error' => 'Oficio no válido'], 400);
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
                    'rev_type' => 'tesis',
                    'rev_adviser_rol' => 'presidente', // asesor, presidente, secretario, vocal
                ]);

                $secretario =  Review::create([
                    'adviser_id' => $request->input('secretario'),
                    'student_id' => $docof->student_id,
                    'rev_num_of' => null, 
                    'rev_count' => 0,
                    'rev_status' => 'pendiente', // Estado
                    'rev_type' => 'tesis',
                    'rev_adviser_rol' => 'secretario', // asesor, presidente, secretario, vocal
                ]);

                $vocal =  Review::create([
                    'adviser_id' => $request->input('vocal'),
                    'student_id' => $docof->student_id,
                    'rev_num_of' => null, 
                    'rev_count' => 0,
                    'rev_status' => 'pendiente', // Estado
                    'rev_type' => 'tesis',
                    'rev_adviser_rol' => 'vocal', // asesor, presidente, secretario, vocal
                ]);

                $presidente->save();
                $secretario->save();
                $vocal->save();

                
                $docof->update([
                    'of_status' => $request->input('estado'),
                    'of_num_of' => $request->input('numero_oficio'),
                    'of_num_exp' => $request->input('expediente'),
                    'of_observation' => null,
                ]);

                

                return response()->json([
                    'message' => 'Observacion enviada y actualizada',
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
                    'revision_presidente_id' => $presidente->_id,
                    'revision_secretario_id' => $secretario->_id,
                    'revision_vocal_id' => $vocal->_id,
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
}
<?php

namespace App\Http\Controllers\Api;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Http\Resources\SolicitudeResource;
use App\Models\Adviser;
use App\Models\Solicitude;
use App\Models\Student;
use App\Models\History;
use App\Models\DocOf;
use App\Models\DocResolution;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class SolicitudeController extends Controller
{   
    //Crear solicitude después del registro del estudiante
    public function store(Request $request)
    {
        // Validar que el student_id está presente
        $validatedData = $request->validate([
            'student_id' => 'required|string|exists:students,_id',
        ]);

        // Verificar si ya existe una solicitud en estado 'pending' o 'in-progress' para este estudiante
        $existingSolicitude = Solicitude::where('student_id', $validatedData['student_id'])
                                        ->whereIn('sol_status', ['pendiente', 'en progreso']) // Estados que deseas evitar duplicar
                                        ->first();

        if ($existingSolicitude) {
            return response()->json([
                'message' => 'El estudiante ya tiene una solicitud en proceso.',
                'data' => $existingSolicitude
            ], 409); // Código 409: Conflict
        }

        // Crear la solicitud si no existe una en estado pendiente
        $solicitude = Solicitude::create([
            'sol_title_inve' => null, // Inicialmente vacío
            'adviser_id' => null, // Inicialmente vacío
            'student_id' => $validatedData['student_id'], // ID del estudiante
            'sol_status' => 'en progreso' // Estado inicial pendiente
        ]);

        return response()->json([
            'status' => true, 
            'message' => 'Se inició tu trámite satisfactoriamente', 
            'data' => $solicitude], 201);
    }

    // Actualizar título de tesis y asesor
    public function updateSolicitude(Request $request, $id)
    {
        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'sol_title_inve' => 'required|string|max:255',
            'adviser_id' => 'required|exists:advisers,_id', // Asumiendo que hay una colección 'advisers'
            'sol_status' => 'required', 
        ]);

        // Si la validación falla
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Buscar la solicitud
            $solicitude = Solicitude::findOrFail($id);

            // Actualizar los campos
            $solicitude->update([
                'sol_title_inve' => $request->input('sol_title_inve'),
                'adviser_id' => $request->input('adviser_id'),
                'sol_status' => $request->input('sol_status'),
            ]);

            return response()->json([
                'message' => 'Solicitud enviada al asesor con exito',
                'data' => $solicitude
            ], 200);
        } catch (\Exception $e) {
            // Manejar cualquier excepción
            return response()->json([
                'message' => 'No se pudo enviar tu solicitud',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    

    // Método para actualizar el estado de la solicitud
    public function updateStatus(Request $request, $id)
    {
        // Validar la entrada
        $rules = [
            'sol_status' => 'required|string|in:pendiente,aceptado,rechazado',
            'sol_observation' => 'nullable|string',
            'sol_num' => 'nullable|string'
        ];

        // Si el estado es "rechazado", la observación debe ser obligatoria
        if ($request->input('sol_status') === 'rechazado') {
            $rules['sol_observation'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        // Buscar la solicitud por ID
        $solicitude = Solicitude::find($id);

        if (!$solicitude) {
            return response()->json([
                'status' => false,
                'message' => 'Solicitud no encontrada'
            ], 404);
        }

        // Acciones en función del estado
        if ($request->input('sol_status') === 'rechazado') {
            // Guardar en History
            History::create([
                'solicitude_id' => $solicitude->_id,
                'action' => $request->input('action', 'rechazado por asesor'),
                'sol_title_inve' => $solicitude->sol_title_inve,
                'observation' => $request->input('sol_observation'),
                'adviser_id' => $solicitude->adviser_id
            ]);

            // Resetear la solicitud
            $solicitude->update([
                'sol_title_inve' => null,
                'adviser_id' => null,
                'sol_status' => 'rechazado',
                // Actualizar la observación
                'sol_observation' => $request->input('sol_observation')
            ]);
        } else {
             // Crear un nuevo registro en la colección docOf si no es "rechazado"
             $docOf = new DocOf([
                'solicitude_id' => $solicitude->_id,
                'of_name' => null,  // Inicializado como null
                'of_num_of' => null,  // Inicializado como null
                'of_num_exp' => null,  // Inicializado como null
                'of_status' => 'pendiente',  // Estado fijo en "pendiente"
                'of_observation' => null  // Inicializado como null
            ]);
            $docOf->save();

            // Actualizar el estado de la solicitud
            $solicitude->update([
                'sol_status' => $request->input('sol_status'),
                'sol_num' => $request->input('sol_num')
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Estado de la solicitud actualizado correctamente',
            'solicitude' => $solicitude
        ], 200);
    }


    //Muesta solicitudes de asesoria aceptados
    public function getSolicitudeForPaisi()
    {
        $solicitudes = Solicitude::where('sol_status', 'aceptado')->get();                                   
        return SolicitudeResource::collection($solicitudes);

    }

    //Muestra solicitudes de un asesor en especifico, por orden pendiente, aceptado, rechazado.
    public function getSolicitudeToAdviser($adviser_id)
    {
        // Recibe el id del Asesor
        $adviser = Adviser::where('_id', $adviser_id)->first();

        // Revisa si el asesor existe
        if (!$adviser) {
            return response()->json(['message' => 'El asesor no existe'], 404);
        }

        // Extrae solicitud por la id del asesor
         $solicitudes = Solicitude::where('adviser_id', $adviser->_id)->get();

        if ($solicitudes->isEmpty()) {
            return response()->json(['message' => 'Este asesor no tiene solicitudes'], 404);
        }

       // Ordenando las solicitudes
        $orden = $solicitudes->sortBy(function ($solicitud) {
            switch ($solicitud->sol_status) {
                case 'pendiente':
                    return 1;
                case 'aceptado':
                    return 2;
                case 'rechazado':
                    return 3;
            }
        });

        // Devuelve los datos de las solicitudes ordenadas
        return response()->json([
            'data' => SolicitudeResource::collection($orden),
        ], 200);
    }

    //Generar PDF de aceptacion de asesor
    public function viewPDF($id) {
        // Obtener el registro específico por su id
        $solicitude = Solicitude::where('_id', $id)->first();
    
        // Verifica si el registro no se encuentra
        if (!$solicitude) {
            return redirect()->back()->with('error', 'Solicitud no encontrada');
        }
    
        // Formatear la fecha updated_at como "11 de julio de 2024"
        $formattedDate = Carbon::parse($solicitude->updated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
    
        // Recibe el id del Asesor
        $adviser = Adviser::where('_id', $solicitude->adviser_id)->first();
    
        // Verifica si el asesor existe
        if ($adviser) {
            // Formatear los nombres del asesor
            $adviserFormatted = [
                'adv_name' => ucwords(strtolower($adviser->adv_name)),
                'adv_lastname_m' => ucwords(strtolower($adviser->adv_lastname_m)),
                'adv_latsname_f' => ucwords(strtolower($adviser->adv_latsname_f)),
            ];
        } else {
            $adviserFormatted = null;
        }

        $student = Student::where('_id', $solicitude->student_id)->first();
    
        // Verifica si el estudiante existe
        if ($student) {
            // Formatear los nombres del estudiante
            $studentFormatted = [
                'stu_name' => ucwords(strtolower($student->stu_name)),
                'stu_lastname_m' => strtoupper($student->stu_lastname_m),
                'stu_latsname_f' => strtoupper($student->stu_latsname_f),
            ];
        } else {
            $studentFormatted = null; 
        }
    
        // Pasar los datos a la vista
        $pdf = Pdf::loadView('letter', compact('solicitude', 'formattedDate', 'adviserFormatted', 'studentFormatted'));
    
        return $pdf->stream();
    }
    
    public function getAll(){
        $solicitudes = Solicitude::get()->toArray();
        return response()->json($solicitudes);
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
            // Guardar en History
            History::create([
                'solicitude_id' => $docOf->solicitude_id,
                'action' => 'observado por PAISI',
                'of_num_of' => $docOf->of_num_of,
                'of_num_exp' => $docOf->of_num_exp,
                'observation' => $request->input('of_observation')
            ]);

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

            
}

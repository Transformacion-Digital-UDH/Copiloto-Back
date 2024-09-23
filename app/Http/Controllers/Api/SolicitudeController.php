<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SolicitudeResource;
use App\Models\Adviser;
use App\Models\Solicitude;
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
        // Definir las reglas de validación
        $rules = [
            'sol_status' => 'required|string|in:pendiente,aceptado,rechazado', // Asume que los estados posibles son 'pendiente', 'aceptado', 'rechazado'
        ];

        // Validar los datos de entrada
        $validator = Validator::make($request->all(), $rules);

        // Verificar si hay errores
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

        // Actualizar el estado de la solicitud
        $solicitude->sol_status = $request->input('sol_status');
        $solicitude->save();

        return response()->json([
            'status' => true,
            'message' => 'Solicitud aceptada correctamente',
            'solicitude' => $solicitude
        ], 200);
    }
    public function getSolicitudeForPaisi()
    {
        $solicitudes = Solicitude::where('sol_status', 'aceptado')->get();                                   
        return SolicitudeResource::collection($solicitudes);

    }

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

    public function getAll(){
        $solicitudes = Solicitude::get()->toArray();
        return response()->json($solicitudes);
    }
}

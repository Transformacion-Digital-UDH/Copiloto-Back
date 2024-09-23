<?php

namespace App\Http\Controllers\Api;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Adviser;
use App\Models\Solicitude;
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
                                        ->whereIn('sol_status', ['pending', 'in-progress']) // Estados que deseas evitar duplicar
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
            'sol_adviser_id' => null, // Inicialmente vacío
            'student_id' => $validatedData['student_id'], // ID del estudiante
            'sol_status' => 'pending' // Estado inicial pendiente
        ]);

        return response()->json(['message' => 'Solicitude created successfully', 'data' => $solicitude], 201);
    }

    // Actualizar título de tesis y asesor
    public function updateSolicitude(Request $request, $id)
    {
        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'sol_title_inve' => 'required|string|max:255',
            'sol_adviser_id' => 'required|exists:advisers,_id', // Asumiendo que hay una colección 'advisers'
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
                'sol_adviser_id' => $request->input('sol_adviser_id'),
            ]);

            return response()->json([
                'message' => 'Solicitude updated successfully',
                'solicitude' => $solicitude
            ], 200);
        } catch (\Exception $e) {
            // Manejar cualquier excepción
            return response()->json([
                'message' => 'Failed to update solicitude',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Método para actualizar el estado de la solicitud
    public function updateStatus(Request $request, $id)
    {
        // Definir las reglas de validación
        $rules = [
            'sol_status' => 'required|string|in:pending,accepted,rejected', // Asume que los estados posibles son 'pendiente', 'aceptado', 'rechazado'
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
                'message' => 'Solicitude not found'
            ], 404);
        }

        // Actualizar el estado de la solicitud
        $solicitude->sol_status = $request->input('sol_status');
        $solicitude->save();

        return response()->json([
            'status' => true,
            'message' => 'Solicitude status updated successfully',
            'solicitude' => $solicitude
        ], 200);
    }
    //Muesta solicitudes de asesoria aceptados
    public function getSolicitudeForPaisi()
    {
        // Extrae solicitud por la id del estudiante con estado pendiente
        $solicitudes = Solicitude::where('sol_status', 'Aceptado')->get();
                                    
        // Devolver los datos del estudiante junto con sus solicitudes
        return response()->json($solicitudes, 200);
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
         $solicitudes = Solicitude::where('sol_adviser_id', $adviser->_id)->get();

        if ($solicitudes->isEmpty()) {
            return response()->json(['message' => 'Este asesor no tiene solicitudes'], 404);
        }

       // Ordenando las solicitudes
        $orden = $solicitudes->sortBy(function ($solicitud) {
            switch ($solicitud->sol_status) {
                case 'Pendiente':
                    return 1;
                case 'Aceptado':
                    return 2;
                case 'Rechazado':
                    return 3;
            }
        });

        // Devuelve los datos de las solicitudes ordenadas
        return response()->json([
            'solicitudes' => $orden->values(),
        ], 200);
    }

    //Generar PDF de aceptacion de asesor
    public function verPDF($id) {
        // Obtener el registro específico por su id
        $solicitude = Solicitude::where('_id', $id)->first();
    
        // Verifica si el registro no se encuentra
        if (!$solicitude) {
            return redirect()->back()->with('error', 'Dato no encontrado');
        }
    
         // Formatear la fecha updated_at como "Huánuco, 11 de julio de 2024"
        // Formatear la fecha updated_at como "Huánuco, 11 de julio de 2024"
        $formattedDate = Carbon::parse($solicitude->updated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');

        // Pasar la fecha formateada a la vista
        $pdf = Pdf::loadView('letter-accepted', compact('solicitude', 'formattedDate'));
    
        // Opcional: establecer opciones de DomPDF
        $pdf->setOptions(['isHtml5ParserEnabled' => true]);
    
        return $pdf->stream();
    }
}

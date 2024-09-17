<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Solicitude;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SolicitudeController extends Controller
{
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


}

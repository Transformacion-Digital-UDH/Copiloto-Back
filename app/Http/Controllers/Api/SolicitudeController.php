<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Solicitude;
use Illuminate\Http\Request;

class SolicitudeController extends Controller
{
    // Método para registrar una nueva solicitud con el título de la tesis
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'student_id' => 'required|exists:students,_id', // Verificar que el estudiante existe
        ]);

        $solicitude = Solicitude::create([
            'title' => $validatedData['title'],
            'student_id' => $validatedData['student_id'],
            // Otros campos si es necesario
        ]);

        return response()->json(['message' => 'Tesis registrada exitosamente', 'solicitude' => $solicitude], 201);
    }

    // Método para actualizar el título de la tesis en una solicitud existente
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $solicitude = Solicitude::findOrFail($id);
        $solicitude->update([
            'title' => $validatedData['title'],
        ]);

        return response()->json(['message' => 'Título de tesis actualizado', 'solicitude' => $solicitude]);
    }
}

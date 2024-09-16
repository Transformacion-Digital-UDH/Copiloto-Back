<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SolicitudeController extends Controller
{
    public function registerThesis(Request $request)
    {
        $request->validate([
            'thesis_title' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return response()->json(['message' => 'Estudiante no encontrado.'], 404);
        }

        // Verificar si el usuario tiene el rol de student
        if ($user->role !== 'student') {
            return response()->json(['message' => 'Acción no permitida.'], 403);
        }

        if ($student->thesis_title) {
            return response()->json(['message' => 'Ya tienes un proyecto de tesis registrado.'], 400);
        }

        $student->thesis_title = $request->thesis_title;
        $student->thesis_status = 'En Revisión';
        $student->document_url = $request->document_url;
        $student->save();

        return response()->json(['message' => 'Proyecto de tesis registrado correctamente.']);
    }
}

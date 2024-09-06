<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; 
use App\Models\Student; 

class StudentController extends Controller
{
    public function selectAdvisor(Request $request)
    {
        $request->validate([
            'advisor_id' => 'required|exists:users,id',
        ]);

        $user = auth()->user();
        $student = $user->student;

        // Verificar si el usuario tiene el rol de student
        if ($user->role !== 'student') {
            return response()->json(['message' => 'Acción no permitida.'], 403);
        }

        if ($student->advisor_id) {
            return response()->json(['message' => 'Ya tienes un asesor asignado.'], 400);
        }

        $advisor = User::find($request->advisor_id);

        // Verificar si el asesor es del tipo adecuado y está disponible
        if ($advisor->role !== 'advisor' || !$advisor->available) {
            return response()->json(['message' => 'Este asesor no está disponible.'], 400);
        }

        $student->advisor_id = $advisor->id;
        $student->save();

        // Actualizar disponibilidad del asesor
        $advisor->students_assigned[] = $student->id;
        $advisor->available = count($advisor->students_assigned) < 5;
        $advisor->save();

        return response()->json(['message' => 'Asesor asignado correctamente.']);
    }

    public function registerThesis(Request $request)
    {
        $request->validate([
            'thesis_title' => 'required|string|max:255',
            'document_url' => 'required|url',
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

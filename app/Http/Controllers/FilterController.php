<?php

namespace App\Http\Controllers;

use App\Models\Filter;
use App\Models\Review;
use App\Models\Student;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function createReviewVRI ($student_id){
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
            ->where('rev_type', 'informe')
            ->where('rev_status', 'aprobado')
            ->first();
        $secretario = Review::where('student_id', $student_id)
            ->where('rev_adviser_rol', 'secretario')
            ->where('rev_type', 'informe')
            ->where('rev_status', 'aprobado')
            ->first();
        $vocal = Review::where('student_id', $student_id)
            ->where('rev_adviser_rol', 'vocal')
            ->where('rev_type', 'informe')
            ->where('rev_status', 'aprobado')
            ->first();

        if (!$presidente || !$secretario || !$vocal) {
            return response()->json([
                'mensaje' => 'El estudiante aún no tiene la conformidad de sus jurados',
            ], 400);
        }

        // Verificar si ya existe una solicitud de "Aprobación de tesis" pendiente
        $exist = Filter::where('student_id', $student_id)
            ->where('fil_name', 'primer filtro')
            ->first();

        if ($exist) {response()->json([
                'mensaje' => 'El estudiante ya tiene una revision en proceso',
            ], 400);
        }

        // Crear nueva solicitud de aprobación de tesis
        $filter = Filter::create([
            'student_id' => $student_id,
            'fil_name' => 'primer filtro',
            'fil_status' => 'pendiente',
            'fil_file' => null,
        ]);

        // Guardar el nuevo documento en la base de datos
        return response()->json([
            'mensaje' => 'Aprobación de informe creada correctamente',
            'estado' => $office->of_status,
        ], 200);
    }
}

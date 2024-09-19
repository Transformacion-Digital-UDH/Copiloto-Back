<?php

namespace App\Http\Controllers;

use App\Models\DocOf;
use App\Models\DocResolution;
use App\Models\Solicitude;
use App\Models\Student;
use Illuminate\Http\Request;
use MongoDB\Laravel\Eloquent\Casts\ObjectId;

class StudentController extends Controller
{
    public function getAllStudents()
    {
        $student = Student::get()->toArray();
        return response()->json($student);
    }

  
    public function getInfoStudentById($student_id)
    {
        // Recibe el id del estudiante y busca
        $student = Student::where('_id', $student_id)->first();

        if (!$student) {
            return response()->json(['message' => 'El estudiante no existe'], 404);
        }

        // Extrae solicitud por la id del estudiante con estado pendiente
        $solicitudes = Solicitude::where('sol_student_id', $student->_id)
                                    ->where('sol_status', 'Pendiente')->get();
                                    
        if ($solicitudes->isEmpty()) {
            return response()->json(['message' => 'Este estudiante no inició su trámite'], 404);
        }

        // Busca Documentos Oficio por id del estudiante
        $doc_of = DocOf::where('docof_student_id', $student->_id)->get();

        if ($doc_of->isEmpty()) {
            return response()->json([
                'student' => $student,
                'solicitudes' => $solicitudes,
                'message' => 'Este estudiante no tiene oficios ni resoluciones'
            ], 200);
        }

        // Busca Documentos Resolucion por id del estudiante
        $doc_resolutions = DocResolution::where('docres_student_id', $student->_id)->get();

        if ($doc_resolutions->isEmpty()) {
                return response()->json([
                    'student' => $student,
                    'solicitudes' => $solicitudes,
                    'oficios' => $doc_of,
                    'message' => 'Este estudiante no tiene resoluciones'
                ], 200);
        }
        

        // Devolver los datos del estudiante junto con sus solicitudes
        return response()->json([
            'student' => $student,
            'solicitudes' => $solicitudes,
            'oficios' => $doc_of,
            'resoluciones' => $doc_resolutions,
        ], 200);
    }
        
}

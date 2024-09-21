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

        // Obtener la ultima solicitud creada por el estudiante
        $solicitudes = Solicitude::where('student_id', $student->_id)->get();

        $solicitudePending = Solicitude::where('student_id', $student->_id)
                            ->first();

        if (!$solicitudePending) {
            return response()->json([
                'status' => false,
                'message' => 'Este estudiante no inició su trámite'
            ], 404);
        }

        // // Busca Documentos Oficio por id del estudiante
        // $doc_of = DocOf::where('docof_student_id', $student->_id)->get();

        // if ($doc_of->isEmpty()) {
        //     return response()->json([
        //         'student' => $student,
        //         'solicitudes' => $solicitudes,
        //         'message' => 'Este estudiante no tiene oficios ni resoluciones'
        //     ], 200);
        // }

        // // Busca Documentos Resolucion por id del estudiante
        // $doc_resolutions = DocResolution::where('docres_student_id', $student->_id)->get();

        // if ($doc_resolutions->isEmpty()) {
        //         return response()->json([
        //             'student' => $student,
        //             'solicitudes' => $solicitudes,
        //             'oficios' => $doc_of,
        //             'message' => 'Este estudiante no tiene resoluciones'
        //         ], 200);
        // }


        // Devolver los datos del estudiante junto con sus solicitudes
        return response()->json([
            'status' => true,
            'solicitude_pendiente' => [
                "id" => $solicitudePending->_id,
                "titulo" => $solicitudePending->sol_title_inve,
                "asesor_id" => $solicitudePending->adviser_id,
                "estado" => $solicitudePending->sol_status,
            ],
            'solicitudes' => $solicitudes,
            // 'oficios' => $doc_of,
            // 'resoluciones' => $doc_resolutions,
        ], 200);
    }
}

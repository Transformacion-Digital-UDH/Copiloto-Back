<?php

namespace App\Http\Controllers;

use App\Http\Resources\DocOfResource;
use App\Http\Resources\DocResolutionResource;
use App\Http\Resources\HistoryResource;
use App\Models\DocOf;
use App\Models\DocResolution;
use App\Models\Solicitude;
use App\Models\Student;
use Illuminate\Http\Request;
use MongoDB\Laravel\Eloquent\Casts\ObjectId;

class StudentController extends Controller
{
    public function getAll()
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
        $solicitude = Solicitude::where('student_id', $student->_id)
            ->first();

        if (!$solicitude) {
            return response()->json([
                'status' => false,
                'message' => 'Este estudiante no inició su trámite'
            ], 404);
        }
        
        $docof = DocOf::where('solicitude_id', $solicitude->_id)->first();
        $resolution = [];

        if(!$docof){
            $office = [];
        }else{
            $office = new DocOfResource($solicitude->docof);
            $resdoc = DocResolution::where('docof_id', $docof->_id)->first();
           
            if($resdoc){
                $resolution = new DocResolutionResource(DocResolution::where('docof_id', $docof->_id)->first());
            }
        }

        // Devolver los datos del estudiante junto con sus solicitudes
        return response()->json([
            'status' => true,
            'solicitud' => [
                "id" => $solicitude->_id,
                "titulo" => $solicitude->sol_title_inve,
                "asesor_id" => $solicitude->adviser_id,
                "estado" => $solicitude->sol_status,
                "observacion" => $solicitude->sol_observation,
                "link" => $solicitude->document_link,
            ],
            'historial' => HistoryResource::collection($solicitude->history),
            'oficio' => $office,
            'resolucion' => $resolution
        ], 200);
    }
}

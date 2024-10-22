<?php

namespace App\Http\Controllers;

use App\Http\Resources\DocOfResource;
use App\Http\Resources\DocResolutionResource;
use App\Http\Resources\HistoryResource;
use App\Models\DocOf;
use App\Models\DocResolution;
use App\Models\Solicitude;
use App\Models\Student;
use App\Models\Adviser;
use App\Models\Review;
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
        $solicitude = Solicitude::where('student_id', $student->_id)->first();

        if (!$solicitude) {
            return response()->json([
                'status' => false,
                'message' => 'Este estudiante no inició su trámite'
            ], 404);
        }
        
        $docof = DocOf::where('solicitude_id', $solicitude->_id)->first();
        $resolution = [];

        if (!$docof) {
            $office = [];
        } else {
            $office = new DocOfResource($solicitude->docof);
            $resdoc = DocResolution::where('docof_id', $docof->_id)->first();
            
            if ($resdoc) {
                $resolution = new DocResolutionResource($resdoc);
            }
        }

        // Obtener información del asesor
        $adviser = null;
        if ($solicitude->adviser_id) {
            $adviser = Adviser::find($solicitude->adviser_id); // Asegúrate de que Adviser sea el modelo correcto
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
                "tipo_investigacion" => $solicitude->sol_type_inve,
                "asesor" => $adviser ? [
                    "nombre" => $adviser->adv_name,
                    "apellido_paterno" => $adviser->adv_lastname_f,
                    "apellido_materno" => $adviser->adv_lastname_m,
                ] : null, // Si el asesor no se encuentra, retorna null
            ],
            'historial' => HistoryResource::collection($solicitude->history),
            'oficio' => $office,
            'resolucion' => $resolution
        ], 200);
    }

    public function viewJuriesForTesisByStudent($student_id) {
        // Obtener todas las revisiones relacionadas con el estudiante especificado
        $reviews = Review::where('student_id', $student_id)
            ->where('rev_adviser_rol', '!=', 'asesor')
            ->get();  
        
        // Crear un array para almacenar asesores y roles
        $jurados = $reviews->map(function($review) {
            // Obtener el asesor correspondiente a la revisión
            $adviser = Adviser::where('_id', $review->adviser_id)->first(); // Obtener el asesor
            
            // Verificar si el asesor existe
            $adviser_name = $adviser ? strtoupper($adviser->adv_lastname_m . ' ' . $adviser->adv_lastname_f . ', ' . $adviser->adv_name) : 'No disponible';
    
            return [
                'asesor' => $adviser_name, // Convertir a mayúsculas
                'rol' => $review->rev_adviser_rol
            ];
        });
    
        // Obtener el documento de oficio
        $docof = DocOf::where('student_id', $student_id)
            ->where('of_name', 'Solicitud de jurados para revision de tesis')
            ->first();
    
        // Verificar si el documento existe antes de acceder a sus propiedades
        if (!$docof) {
            return response()->json([
                'estudiante_id' => $student_id,
                'mensaje' => 'No ha iniciado el trámite, complete el pre-requisito.',
                'estado' => 'No iniciado',
                'jurados' => $jurados,
            ], 404); // O cualquier otro código de estado que consideres apropiado
        }
    
        // Retornar los roles y IDs en formato JSON
        return response()->json([
            'estudiante_id' => $student_id,
            'tramite' => $docof->of_name,
            'estado' => $docof->of_status,
            'docof_id' => $docof->_id,
            'jurados' => $jurados,
        ], 200);
    }

    public function getInfoApproveThesis($student_id){
        $docof = DocOf::where('student_id', $student_id)
            ->where('of_name', 'Aprobación de tesis')
            ->first();

        if(!$docof){
            return response()->json([
                'mensaje' => 'Proceso no iniciado',
            ], 400);
        }

        $docres = DocResolution::where('docof_id', $docof->_id)
            ->first();

        if(!$docres){
            return response()->json([
                'estudiante_id' => $student_id,
                'oficio_id' => $docof->_id,
                'oficio_estado' => $docof->of_status,
                'resolucion_id' => $docres->_id ?? '',
                'resolucion_estado' => 'no iniciado',
            ], 200);
        }
        return response()->json([
            'estudiante_id' => $student_id,
            'oficio_id' => $docof->_id,
            'oficio_estado' => $docof->of_status,
            'resolucion_id' => $docres->_id,
            'resolucion_estado' => $docres->docres_status,
        ], 200);
    }

    public function getInfoEjecucion($student_id) {
        $student = Student::where('_id', $student_id)->first();
        $student_name = $student->stu_lastname_m . ' ' . $student->stu_lastname_f . ' ' . $student->stu_name;
        $res_da = Solicitude::where('student_id', $student_id)->first();

        return response()->json([
            'nombre_estudiante' => $student_name,
            'fecha_ini' => $date_ini,
            'fecha_fin' => $date_end,
        ], 200);

    }
}

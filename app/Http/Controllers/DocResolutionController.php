<?php

namespace App\Http\Controllers;

use App\Models\Adviser;
use App\Models\DocOf;
use App\Models\DocResolution;
use App\Models\Solicitude;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocResolutionController extends Controller
{
    public function resPDF($id) {
        $resolution = DocResolution::where('_id', $id)->first();
        // Verifica si el registro no se encuentra
        if (!$resolution) {
            return redirect()->back()->with('error', 'Solicitud no encontrada');
        }
        // Formatear la fecha updated_at como "11 de julio de 2024"
        $formattedDate = Carbon::parse($resolution->updated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
        $year_res = Carbon::parse($resolution->updated_at)->locale('es')->isoFormat('YYYY');

        $office = DocOf::where('_id', $resolution->docof_id)->first(); 
        $year_of = Carbon::parse($office->updated_at)->locale('es')->isoFormat('YYYY');

        $solicitude = Solicitude::where('_id', $office->solicitude_id)->first();
        // Recibe el id del Asesor
        $adviser = Adviser::where('_id', $solicitude->adviser_id)->first();
        // Verifica si el asesor existe
        if ($adviser) {
            // Formatear los nombres del asesor
            $adviserFormatted = [
                'adv_name' => ucwords(strtolower($adviser->adv_name)),
                'adv_lastname_m' => ucwords(strtolower($adviser->adv_lastname_m)),
                'adv_lastname_f' => ucwords(strtolower($adviser->adv_lastname_f)),
            ];
        } else {
            $adviserFormatted = null;
        }
        if ($adviser) {
            // Concatenar las primeras letras de los nombres y apellidos
            $siglas = strtoupper(
                mb_substr($adviser->adv_name, 0, 1) .
                mb_substr($adviser->adv_lastname_m, 0, 1) .
                mb_substr($adviser->adv_lastname_f, 0, 1)
            );
        } else {
            $siglas = null;
        }
        $student = Student::where('_id', $solicitude->student_id)->first();
        // Verifica si el estudiante existe
        if ($student) {
            // Formatear los nombres del estudiante
            $studentFormatted = [
                'stu_name' => ucwords(strtolower($student->stu_name)),
                'stu_lastname_m' => strtoupper($student->stu_lastname_m),
                'stu_lastname_f' => strtoupper($student->stu_lastname_f),
            ];
        } else {
            $studentFormatted = null; 
        }
        // Pasar los datos a la vista
        $pdf = Pdf::loadView('resolution_adviser', compact('siglas', 'resolution', 'office', 'formattedDate', 'adviserFormatted', 'studentFormatted', 'year_of', 'year_res'));
        return $pdf->stream(); // Puedes especificar un nombre para el archivo PDF
}

    public function downloadResolution($id) {
        $resolution = DocResolution::where('_id', $id)->first();
        // Verifica si el registro no se encuentra
        if (!$resolution) {
            return redirect()->back()->with('error', 'Solicitud no encontrada');
        }
        // Formatear la fecha updated_at como "11 de julio de 2024"
        $formattedDate = Carbon::parse($resolution->updated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
        $year_res = Carbon::parse($resolution->updated_at)->locale('es')->isoFormat('YYYY');

        $office = DocOf::where('_id', $resolution->docof_id)->first(); 
        $year_of = Carbon::parse($office->updated_at)->locale('es')->isoFormat('YYYY');

        $solicitude = Solicitude::where('_id', $office->solicitude_id)->first();
        // Recibe el id del Asesor
        $adviser = Adviser::where('_id', $solicitude->adviser_id)->first();
        // Verifica si el asesor existe
        if ($adviser) {
            // Formatear los nombres del asesor
            $adviserFormatted = [
                'adv_name' => ucwords(strtolower($adviser->adv_name)),
                'adv_lastname_m' => ucwords(strtolower($adviser->adv_lastname_m)),
                'adv_lastname_f' => ucwords(strtolower($adviser->adv_lastname_f)),
            ];
        } else {
            $adviserFormatted = null;
        }
        if ($adviser) {
            // Concatenar las primeras letras de los nombres y apellidos
            $siglas = strtoupper(
                mb_substr($adviser->adv_name, 0, 1) .
                mb_substr($adviser->adv_lastname_m, 0, 1) .
                mb_substr($adviser->adv_lastname_f, 0, 1)
            );
        } else {
            $siglas = null;
        }
        $student = Student::where('_id', $solicitude->student_id)->first();
        // Verifica si el estudiante existe
        if ($student) {
            // Formatear los nombres del estudiante
            $studentFormatted = [
                'stu_name' => ucwords(strtolower($student->stu_name)),
                'stu_lastname_m' => strtoupper($student->stu_lastname_m),
                'stu_lastname_f' => strtoupper($student->stu_lastname_f),
            ];
        } else {
            $studentFormatted = null; 
        }
        // Pasar los datos a la vista
        $pdf = Pdf::loadView('resolution_adviser', compact('siglas', 'resolution', 'office', 'formattedDate', 'adviserFormatted', 'studentFormatted', 'year_of', 'year_res'));
        return $pdf->download($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ' ' . $student->stu_name . ' RES-DA.pdf'); // Puedes especificar un nombre para el archivo PDF
    }

    public function updateStatus(Request $request, $id)
        {
            // Validar la entrada
            $rules = [
                'docres_status' => 'required|string|in:pendiente,tramitado,observado',
                'docres_observation' => 'nullable|string',
                'docres_num_res' => 'nullable|string'
            ];

            // Si el estado es "rechazado", la observación debe ser obligatoria
            if ($request->input('docres_status') === 'observado') {
                $rules['docres_observation'] = 'required|string';
            }

            if ($request->input('docres_status') === 'tramitado') {
                $rules['docres_num_res'] = 'required|string';
            }


            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 400);
            }

            // Buscar la solicitud por ID
            $resolution = DocResolution::where('docof_id', $id)->first();

            if (!$resolution) {
                return response()->json([
                    'status' => false,
                    'message' => 'Resolucion no encontrada'
                ], 404);
            }

            // Acciones en función del estado
            if ($request->input('docres_status') === 'observado') {
                // Actualizar estado a Observado y actualizar docres_observation
                
                $resolution->update([
                    'docres_status' => 'observado',
                    // Actualizar la observación
                    'docres_observation' => $request->input('docres_observation')
                ]);
            
            }
            elseif ($request->input('docres_status') === 'tramitado') {
                    // Actualizar estado a Tramitado y limpiar docres_observation
                    $resolution->update([
                        'docres_status' => 'tramitado',
                        'docres_num_res' => $request->input('docres_num_res'),
                        'docres_observation' => null
                    ]);
                }

            return response()->json([
                'status' => true,
                'message' => 'Estado de la resolución actualizado correctamente',
                'resolution' => $resolution
            ], 200);
        }

        public function getReslutionApproveThesis()
        {
            $resolutions = DocResolution::where('docres_name', 'Aprobación de tesis')->get(); // Cambié 'docof_name' a 'docres_name', asumiendo que 'docof_name' es incorrecto.

            // Definir el orden deseado
            $order = ['pendiente', 'observado', 'tramitado'];

            // Ordenar manualmente las solicitudes por 'docres_status' (asumiendo que es el campo correcto)
            $sorted_resolution = $resolutions->sort(function ($a, $b) use ($order) {
                return array_search($a->docres_status, $order) <=> array_search($b->docres_status, $order);
            })->values();

            // Crear un array para almacenar los resultados finales
            $result = [];

            // Recorrer cada solicitud ordenada y obtener los datos del estudiante y de la solicitud
            foreach ($sorted_resolution as $resolution) {
                // Obtener la solicitud relacionada con DocOf
                $office = DocOf::find($resolution->docof_id); // Cambié 'where' a 'find' para buscar por ID directamente

                // Obtener el estudiante relacionado
                $student = Student::find($office->student_id);

                // Obtener la solicitud relacionada al título del estudiante
                $tittle = Solicitude::where('student_id', $office->student_id)->first(); // Corrijo para usar 'student_id' de 'office'

                // Agregar los resultados al array final
                $result[] = [
                    'resolucion_id' => $resolution->_id,
                    'oficio_id' => $office->_id,
                    'nombre' => ucwords(strtolower($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name)),
                    'titulo' => $tittle->sol_title_inve,
                    'estado' => $resolution->docres_status, // Cambié 'of_status' a 'docres_status', asumiendo que es el campo correcto
                ];
            }

            return response()->json($result);
        }

        public function updateStatusResolutionApproveThesis(Request $request, $docres_id)
        {
            // Obtener el registro correspondiente en la base de datos
            $docres = DocResolution::where('_id', $docres_id)->first();

            if (!$docres) {
                return response()->json(['error' => 'Resolucion no encontrada'], 404);
            }
            
            $state = $request->input('estado');

            $rules = [
                'estado' => 'required|string|in:observado,tramitado',
            ];
            // Manejo de diferentes estados usando un switch
            switch ($state) {
                case 'observado':
                    $rules['observacion'] = 'required|string'; // Agrega la regla para rev_num_of
                    $this->validate($request, ['observacion' => $rules['observacion']]);
                    
                    $docres->update([
                        'docres_status' => $request->input('estado'),
                        'docres_observation' => $request->input('observacion')
                    ]);
                    return response()->json([
                        'message' => 'Observacion enviada y actualizada',
                        'observacion' => $docres->docres_observation,
                        'estado' => $docres->docres_status,
                    ], 200);

                    break;

                case 'tramitado':

                    $rules = [
                        'numero_resolucion' => 'required|string',
                    ];

                    $validator = Validator::make($request->all(), $rules);

                    if ($validator->fails()) {
                        return response()->json([
                            'status' => false,
                            'errors' => $validator->errors()
                        ], 400);
                    }
                    
                    $this->validate($request, $rules);
                    
                    $docres->update([
                        'docres_status' => $request->input('estado'),
                        'docres_num_res' => $request->input('numero_resolucion'),
                        'docres_observation' => null,
                    ]);

                    return response()->json([
                        'message' => 'Resolución trámitada',
                        'estado' => $docres->docres_status,
                    ], 200);
                
                    break;

                default:
                    // Manejo de estado no reconocido
                    return response()->json(['error' => 'Estado no válido'], 400);
        }

        $docres->save();

        }

}

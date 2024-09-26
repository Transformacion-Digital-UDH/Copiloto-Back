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
                'adv_latsname_f' => ucwords(strtolower($adviser->adv_latsname_f)),
            ];
        } else {
            $adviserFormatted = null;
        }
        $student = Student::where('_id', $solicitude->student_id)->first();
        // Verifica si el estudiante existe
        if ($student) {
            // Formatear los nombres del estudiante
            $studentFormatted = [
                'stu_name' => ucwords(strtolower($student->stu_name)),
                'stu_lastname_m' => strtoupper($student->stu_lastname_m),
                'stu_latsname_f' => strtoupper($student->stu_latsname_f),
            ];
        } else {
            $studentFormatted = null; 
        }
        // Pasar los datos a la vista
        $pdf = Pdf::loadView('resolution_adviser', compact('resolution', 'office', 'formattedDate', 'adviserFormatted', 'studentFormatted', 'year_of', 'year_res'));
        return $pdf->stream(); // Puedes especificar un nombre para el archivo PDF
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
        $resolution = DocResolution::find($id);

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


}

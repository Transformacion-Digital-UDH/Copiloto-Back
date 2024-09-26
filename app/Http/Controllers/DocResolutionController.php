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

}

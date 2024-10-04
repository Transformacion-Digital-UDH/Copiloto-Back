<?php

namespace App\Http\Controllers;

use App\Models\Adviser;
use App\Models\HistoryReview;
use App\Models\Review;
use App\Models\Solicitude;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class HistoryReviewController extends Controller
{
    public function viewRevisionByStudent($student_id) {
        // Obtiene solo los campos rev_file, rev_count, student_id, adviser_id y updated_at, ordenados por rev_count
        $reviews = HistoryReview::where('student_id', $student_id)
                                 ->orderBy('rev_count', 'desc')
                                 ->select('rev_file', 'rev_count', 'updated_at', 'rev_status') // Selecciona los campos necesarios
                                 ->get();
        
        // Transformamos los datos
        $history = $reviews->map(function ($review) {
            return [
                'rev_file' => $review->rev_file,
                'rev_count' => $review->rev_count,
                'updated_at' => Carbon::parse($review->updated_at)->format('d/m/Y | H:i:s'), // Formato personalizado
                'rev_status' => $review->rev_status,
            ];
        });
          
        $review = Review::where('student_id', $student_id)->first();
        
        if (!$review) {
            return response()->json([
                'status' => false,
                'message' => 'No tiene ninguna solicitud de revisión'
            ]);
        }

        $solicitude = Solicitude::where('student_id', $student_id)->first();

        $adviser = Adviser::where('_id', $solicitude->adviser_id)->first(); 

        $adviser_name = $adviser->adv_lastname_m . ' ' . $adviser->adv_lastname_f . ', ' . $adviser->adv_name;

        
        return response()->json([
            'status' => true,
            'data' => [
                'asesor' => $adviser_name,
                'título' => $solicitude->sol_title_inve,
                'link-tesis' => $solicitude->document_link,
            ],
            'revision' => [
                'revision_id' => $review->_id,
                'estudiante_id' => $review->student_id,
                'estado' => $review->rev_status,
                'cantidad' => $review->rev_count,
                'archivos' => $review->rev_file,
                'fecha' => Carbon::parse($review->updated_at)->format('d/m/Y | H:i:s'), // Formato personalizado
            ],
            'historial' => $history
        ]);
    }
    public function viewConfAdviser($id) {

        $review = HistoryReview::where('_id', $id)->first();
    
        // Verifica si el registro no se encuentra
        if (!$review || $review->rev_status != 'aprobado') {
            return redirect()->back()->with('error', 'Solicitud no encontrada o no está aprobada');
        }
    
        $formattedDate = Carbon::parse($review->updated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
        $year = Carbon::parse($review->updated_at)->locale('es')->isoFormat('YYYY');
        
        // Recibe el id del Asesor
        $adviser = Adviser::where('_id', $review->adviser_id)->first();
    
        // Verifica si el asesor existe
        if ($adviser) {
            // Formatear los nombres del asesor (corrigiendo los nombres de los atributos)
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
    
        $student = Student::where('_id', $review->student_id)->first();
        $solicitude = Solicitude::where('student_id', $student->_id)->first();
    
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
        $pdf = Pdf::loadView('CPA_tesis', compact('solicitude', 'review', 'siglas', 'formattedDate', 'adviserFormatted', 'studentFormatted', 'year'));
    
        return $pdf->stream(); // Puedes especificar un nombre para el archivo PDF
    }
    
    public function downloadConfAdviser($id) {

        $review = HistoryReview::where('_id', $id)->first();
    
        // Verifica si el registro no se encuentra
        if (!$review || $review->rev_status != 'aprobado') {
            return redirect()->back()->with('error', 'Solicitud no encontrada o no está aprobada');
        }
    
        $formattedDate = Carbon::parse($review->updated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
        $year = Carbon::parse($review->updated_at)->locale('es')->isoFormat('YYYY');
        
        // Recibe el id del Asesor
        $adviser = Adviser::where('_id', $review->adviser_id)->first();
    
        // Verifica si el asesor existe
        if ($adviser) {
            // Formatear los nombres del asesor (corrigiendo los nombres de los atributos)
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
    
        $student = Student::where('_id', $review->student_id)->first();
        $solicitude = Solicitude::where('student_id', $student->_id)->first();
    
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
        $pdf = Pdf::loadView('CPA_tesis', compact('solicitude', 'review', 'siglas', 'formattedDate', 'adviserFormatted', 'studentFormatted', 'year'));
    
        return $pdf->download($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ' ' . $student->stu_name . ' CPA-TESIS.pdf'); // Puedes especificar un nombre para el archivo PDF

    }
}

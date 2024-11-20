<?php

namespace App\Http\Controllers;

use App\Models\Defense;
use App\Models\Review;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DefenseController extends Controller
{
    public function updateStatusDefense(Request $request,$sus_id){
        
        $sustentacion = Defense::where('_id', $sus_id)
                        ->where('def_status', 'pendiente')
                        ->first();
    
        // Verifica si se encontró la revisión
        if (!$sustentacion) {
            return response()->json([
                'estado' => false,
                'message' => 'Esta sustentación no existe o ya fué emitida'
            ], 404);
        }
        // Define las reglas de validación
        $rules = [
            'sus_estado' => 'required|string|in:pendiente,emitido',
        ]; 
            
        $status = $request->input('sus_estado');
    
        switch ($status) {
            case 'pendiente':
                
                break;
    
            case 'emitido':   
                
                $review_pre = Review::where('student_id', $sustentacion->student_id)
                    ->where('rev_type', 'sustentacion')
                    ->where('rev_adviser_rol', 'presidente')
                    ->first();

                $review_sec = Review::where('student_id', $sustentacion->student_id)
                    ->where('rev_type', 'sustentacion')
                    ->where('rev_adviser_rol', 'secretario')
                    ->first();

                $review_voc = Review::where('student_id', $sustentacion->student_id)
                    ->where('rev_type', 'sustentacion')
                    ->where('rev_adviser_rol', 'vocal')
                    ->first();

                $review_acc = Review::where('student_id', $sustentacion->student_id)
                    ->where('rev_type', 'sustentacion')
                    ->where('rev_adviser_rol', 'accesitario')
                    ->first();

                // Contador para las revisiones existentes
                $valid_reviews_count = 0;

                // Validar cada revisión
                if ($review_pre) $valid_reviews_count++;
                if ($review_sec) $valid_reviews_count++;
                if ($review_voc) $valid_reviews_count++;
                if ($review_acc) $valid_reviews_count++;

                // Verificar si existen al menos 3 revisiones
                if ($valid_reviews_count < 3) {
                    return response()->json(['message' => 'No tiene la calificación de los 3 jurados.'], 404);
                }

                $scores = [];

                // Verificar las revisiones y agregar puntajes si existen
                if ($review_pre && $review_pre->rev_score) {
                    $scores[] = $review_pre->rev_score;
                } else{
                    $review_pre->update([
                        'rev_status' => 'calificado',
                    ]);
                }

                if ($review_sec && $review_sec->rev_score) {
                    $scores[] = $review_sec->rev_score;
                } else if ($review_sec) {
                    $review_sec->update(['rev_status' => 'calificado']);
                }

                if ($review_voc && $review_voc->rev_score) {
                    $scores[] = $review_voc->rev_score;
                } else if ($review_voc) {
                    $review_voc->update(['rev_status' => 'calificado']);
                }

                if ($review_acc && $review_acc->rev_score) {
                    $scores[] = $review_acc->rev_score;
                } else if ($review_acc) {
                    $review_acc->update(['rev_status' => 'calificado']);
                }

                // Si no hay suficientes puntajes, devolver un error
                if (count($scores) < 3) {
                    return response()->json(['message' => 'No se pueden calcular suficientes puntajes válidos.'], 404);
                }

                // Calcular el promedio y redondearlo a un número entero
                $prom_score = round(array_sum($scores) / count($scores));

                // Actualizar el estado de la sustentación
                $sustentacion->update([
                    'def_status' => 'emitido',
                    'def_score' => $prom_score,
                ]);

                return response()->json([
                    'sus_estado' => 'emitido',
                    'sus_mensaje' => 'Acta de sustentación emitida',
                ], 200);


                    break;
    
                default:
                    return response()->json(['message' => 'Estado no válido.'], 400);
            }
    }

    public function viewActDefense($defense_id) {

        
    
        // Pasar los datos a la vista
        $pdf = Pdf::loadView('sus_', compact('siglas', 'office', 'formattedDate', 'adviserFormatted', 'studentFormatted', 'year'));
    
        return $pdf->stream(); // Puedes especificar un nombre para el archivo PDF
    }

}  


<?php

namespace App\Http\Controllers;

use App\Models\Defense;
use App\Models\Review;
use Illuminate\Http\Request;

class DefenseController extends Controller
{
    public function updateStatusReview(Request $request,  $sus_id){
        
        $sustentacion = Defense::where('_id', $sus_id)
                        ->first();
    
        // Verifica si se encontró la revisión
        if (!$sustentacion) {
            return response()->json([
                'estado' => false,
                'message' => 'Esta sustentación no existe'
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
    
            case 'aprobado':   
                
                $review_pre = Review::where('_id', $sustentacion->student_id)
                                    ->where('rev_type','sustentacion')
                                    ->where('rev_adviser_rol','presidente')
                                    ->first();
                
                $review_sec = Review::where('_id', $sustentacion->student_id)
                                    ->where('rev_type','sustentacion')
                                    ->where('rev_adviser_rol','secretario')
                                    ->first();
    
                $review_voc = Review::where('_id', $sustentacion->student_id)
                                    ->where('rev_type','sustentacion')
                                    ->where('rev_adviser_rol','vocal')
                                    ->first();

                $review_acc = Review::where('_id', $sustentacion->student_id)
                                    ->where('rev_type','sustentacion')
                                    ->where('rev_adviser_rol','accesitario')
                                    ->first();
                    break;
    
    
                default:
                    return response()->json(['message' => 'Estado no válido.'], 400);
            }
    }
}  


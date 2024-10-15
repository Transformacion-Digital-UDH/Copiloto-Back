<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdviserResource;
use App\Models\Adviser;
use App\Models\Review;
use App\Models\Solicitude;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdviserController extends Controller
{
    public function getToSelect()
    {
        $advisers = Adviser::get();
        return response()->json([
           'data' =>AdviserResource::collection($advisers)
        ],  200);   
    }

    public function getAll()
    {
        $advisers = Adviser::get();
        return response()->json([
            'data' => $advisers
        ],  200);
    }

    public function getSelectJuriesTesis() {
        // Obtener todos los asesores que tienen 'adv_is_jury' en true
        $advisers = Adviser::where('adv_is_jury', true)->get();
        
        // Obtener los IDs de los asesores
        $adviser_ids = $advisers->pluck('_id');
        
        // Obtener todas las revisiones (reviews) relacionadas con los asesores
        $reviews = Review::whereIn('adviser_id', $adviser_ids)->get();
    
        // Crear un array para almacenar los nombres y IDs de los asesores junto con sus revisiones
        $adviser_info = $advisers->map(function($adviser) use ($reviews) {
            // Filtrar revisiones del asesor actual
            $adviser_reviews = $reviews->where('adviser_id', $adviser->_id);
            
            return [
                'asesor' => strtoupper($adviser->adv_lastname_m . ' ' . $adviser->adv_lastname_f . ', ' . $adviser->adv_name),
                'asesor_id' => $adviser->_id,
                'revisiones' => $adviser_reviews->map(function($review) {
                    // Calcular cuántos días han pasado desde la fecha de creación hasta hoy
                    $days_passed = Carbon::parse($review->created_at)->diffInDays(Carbon::now());
                    
                    // Obtener el estudiante relacionado
                    $student = Student::where('_id', $review->student_id)->first(); // Obtener el primer estudiante
    
                    // Verificar si el estudiante existe
                    $student_name = $student ? strtoupper($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name) : 'No disponible';
    
                    return [
                        'rol' => $review->rev_adviser_rol,
                        'estudiante' => $student_name,
                        'tiempo_dias' => $days_passed, // Días transcurridos
                    ];
                })->values()  // Usar values() para reiniciar los índices
            ];
        });
    
        // Retornar los asesores y las revisiones en la respuesta JSON
        return response()->json([
            'data' => $adviser_info
        ], 200); 
    }
    

    
    
    
}

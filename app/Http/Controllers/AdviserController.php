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
            // Filtrar revisiones del asesor actual que no tienen 'rev_status' igual a 'aprobado'
            $adviser_reviews = $reviews->where('adviser_id', $adviser->_id)
                ->where('rev_status', '!=', 'aprobado');
    
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
    
    public function getReviewInforme($adviser_id)
    {
        // Obtener todas las revisiones con adviser_id y los roles específicos
        $reviews = Review::where('adviser_id', $adviser_id)
                        ->where('rev_type', 'informe')
                        ->whereIn('rev_adviser_rol', ['presidente', 'vocal', 'secretario'])
                        ->get();

        // Si no hay revisiones, devolver un mensaje de error
        if ($reviews->isEmpty()) {
            return response()->json(['message' => 'No tiene revisiones pendientes'], 404);
        }

        // Definir el orden deseado para rev_status
        $statusOrder = ['pendiente', 'observado', 'aprobado'];

        // Ordenar las revisiones por rev_status según el orden personalizado
        $sortedReviews = $reviews->sort(function ($a, $b) use ($statusOrder) {
            $posA = array_search($a->rev_status, $statusOrder);
            $posB = array_search($b->rev_status, $statusOrder);
            return $posA - $posB;
        })->values(); // Resetear las claves

        // Crear una estructura para devolver los resultados con el nombre del estudiante
        $response = [];

        // Recorrer las revisiones ordenadas y buscar al estudiante
        foreach ($sortedReviews as $review) {
            // Buscar el estudiante por su ID
            $my_role = Review::where('adviser_id', $review->adviser_id)->first();
            $student = Student::where('_id', $review->student_id)->first();
            $solicitude = Solicitude::where('student_id', $student->_id)->first();
            $review_presidente = Review::where('student_id', $student->_id)->where('rev_adviser_rol', 'presidente')->where('rev_type', 'informe')->first();
            $review_secretario = Review::where('student_id', $student->_id)->where('rev_adviser_rol', 'secretario')->where('rev_type', 'informe')->first();
            $review_vocal = Review::where('student_id', $student->_id)->where('rev_adviser_rol', 'vocal')->where('rev_type', 'informe')->first();

            // Asegurarse de que el estudiante exista
            if ($student) {
                $response[] = [
                    'nombre' => strtoupper($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name), 
                    'titulo' => $solicitude->sol_title_inve,             
                    'mi_rol' => $my_role->rev_adviser_rol,             
                    'link' => $solicitude->informe_link,
                    'estado' => $review->rev_status, 
                    'revision_id' => $review->_id, 
                    'rol' => $review->rev_adviser_rol,
                    'count' => $review->rev_count,
                    'presidente_estado' => $review_presidente->rev_status ?? '',
                    'presidente_cont' => $review_presidente->rev_count ?? '',
                    'secretario_estado' => $review_secretario->rev_status ?? '',
                    'secretario_cont' => $review_secretario->rev_count ?? '',
                    'vocal_estado' => $review_vocal->rev_status ?? '',
                    'vocal_cont' => $review_vocal->rev_count ?? '',
                ];
            } 
        }
        
        // Devolver las revisiones ordenadas con el nombre del estudiante
        return response()->json([
            'data' => $response
        ], 200);
    }

    
    
    
}

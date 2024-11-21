<?php

namespace App\Http\Controllers;

use App\Models\Adviser;
use App\Models\Defense;
use App\Models\DocOf;
use App\Models\DocResolution;
use App\Models\Review;
use App\Models\Solicitude;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
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
    
        $sus = Defense::where('_id', $defense_id)->first();

        if (!$sus) {
            return response()->json(['error' => 'Defense record not found'], 404);
        }

        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'Spanish_Spain', 'es');
        $sus_dia= utf8_encode(strftime('%A', strtotime($sus->def_fecha)));
        // Formatear la fecha y hora
        $sus_ini = 'siendo las ' . $sus->def_hora . ' del día ' . $sus_dia . strftime(' %d del mes de %B del año %Y', strtotime($sus->def_fecha));


        $student = Student::where('_id', $sus->student_id)->first();
        $student_name = strtoupper($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name);


        $revision_presidente = Review::where('student_id', $student->_id)->where('rev_adviser_rol', 'presidente')->where('rev_type', 'sustentacion')->first();
        $revision_secretario = Review::where('student_id', $student->_id)->where('rev_adviser_rol', 'secretario')->where('rev_type', 'sustentacion')->first();
        $revision_vocal = Review::where('student_id', $student->_id)->where('rev_adviser_rol', 'vocal')->where('rev_type', 'sustentacion')->first();
        $revision_accesitario = Review::where('student_id', $student->_id)->where('rev_adviser_rol', 'accesitario')->where('rev_type', 'sustentacion')->first();


    
        // Recibe el id del Asesor
        $presidente_p = Adviser::where('_id', $revision_presidente->adviser_id)->first();
        $secretario_p = Adviser::where('_id', $revision_secretario->adviser_id)->first();
        $vocal_p = Adviser::where('_id', $revision_vocal->adviser_id)->first();
        $accesitario_p = Adviser::where('_id', $revision_accesitario->adviser_id)->first();


        $presidente = ucwords(strtolower($presidente_p->adv_rank ?? 'Ing.' . ' ' . $presidente_p->adv_name . ' ' . $presidente_p->adv_lastname_m . ' ' . $presidente_p->adv_lastname_f));
        $presidente_rol = strtoupper($revision_presidente->rev_adviser_rol);
        
        $secretario = ucwords(strtolower($secretario_p->adv_rank ?? 'Ing.' . ' ' . $secretario_p->adv_name . ' ' . $secretario_p->adv_lastname_m . ' ' . $secretario_p->adv_lastname_f));
        $secretario_rol = strtoupper($revision_secretario->rev_adviser_rol);

        $vocal = ucwords(strtolower($vocal_p->adv_rank ?? 'Ing.' . ' ' . $vocal_p->adv_name . ' ' . $vocal_p->adv_lastname_m . ' ' . $vocal_p->adv_lastname_f));
        $vocal_rol = strtoupper($revision_vocal->rev_adviser_rol);


        $accesitario = ucwords(strtolower($accesitario_p->adv_rank ?? 'Ing.' . ' ' . $accesitario_p->adv_name . ' ' . $accesitario_p->adv_lastname_m . ' ' . $accesitario_p->adv_lastname_f));
        $accesitario_rol = strtoupper($revision_accesitario->rev_adviser_rol);

               
        $tittle = Solicitude::where('student_id', $student->_id)->first();
        $tittle = mb_strtoupper($tittle->sol_title_inve, 'UTF-8');

        $off = DocOf::where('student_id', $student->_id)->where('of_name', 'designacion de fecha y hora')->first();
        $res = DocResolution::where('docof_id', $off->_id)->first();
        $res_num = $res->docres_num_res;
        $res_year = Carbon::parse($res->updated_at)->locale('es')->isoFormat('YYYY');
        
        $cuantitativo = $sus->def_score;

        if($cuantitativo>10){
            $declare = 'APROBADO';
            $cualitativo = 'SUFICIENTE';
        }
        else{
            $declare = 'DESAPROBADO';
            $cualitativo = 'INSUFICIENTE';
        }

        $emi_hora = $sus->updated_at->format('H:i');
        $emi_date = strftime('%d del mes de %B del año %Y', strtotime($sus->updated_at));

        // Pasar los datos a la vista
        $pdf = Pdf::loadView('sus_acta', compact(
            'sus_ini',
            'emi_hora',
            'emi_date',
            'sus',
            'presidente',
            'presidente_p',
            'presidente_rol',
            'secretario',
            'secretario_p',
            'secretario_rol',
            'vocal',
            'vocal_p',
            'vocal_rol',
            'accesitario',
            'accesitario_p',
            'accesitario_rol',
            'tittle',
            'student_name',
            'res_num',
            'res_year',
            'cuantitativo',
            'declare',
            'cualitativo',
        ));
    
        return $pdf->stream(); // Puedes especificar un nombre para el archivo PDF
    }

}  


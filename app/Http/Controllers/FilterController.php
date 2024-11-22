<?php

namespace App\Http\Controllers;

use App\Models\Filter;
use App\Models\Review;
use App\Models\Student;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function createReviewVRI ($student_id){
        // Verificar si el estudiante existe
        $student = Student::where('_id', $student_id)->first();
        if (!$student) {
            return response()->json([
                'mensaje' => 'El estudiante no existe',
            ], 400);
        }

        // Verificar si el estudiante tiene todos los jurados aprobados
        $presidente = Review::where('student_id', $student_id)
            ->where('rev_adviser_rol', 'presidente')
            ->where('rev_type', 'informe')
            ->where('rev_status', 'aprobado')
            ->first();
        $secretario = Review::where('student_id', $student_id)
            ->where('rev_adviser_rol', 'secretario')
            ->where('rev_type', 'informe')
            ->where('rev_status', 'aprobado')
            ->first();
        $vocal = Review::where('student_id', $student_id)
            ->where('rev_adviser_rol', 'vocal')
            ->where('rev_type', 'informe')
            ->where('rev_status', 'aprobado')
            ->first();

        if (!$presidente || !$secretario || !$vocal) {
            return response()->json([
                'mensaje' => 'El estudiante aún no tiene la conformidad de sus jurados',
            ], 400);
        }

        // Verificar si ya existe una solicitud de "Aprobación de tesis" pendiente
        $exist = Filter::where('student_id', $student_id)
            ->where('fil_name', 'primer filtro')
            ->first();

        if ($exist) {response()->json([
                'mensaje' => 'El estudiante ya tiene una revision en proceso',
            ], 400);
        }

        // Crear nueva solicitud de aprobación de tesis
        $filter = Filter::create([
            'student_id' => $student_id,
            'fil_name' => 'primer filtro',
            'fil_status' => 'pendiente',
            'fil_file' => null,
        ]);

        // Guardar el nuevo documento en la base de datos
        return response()->json([
            'mensaje' => 'Revision en Primer filtro',
            'estado' => $filter->fil_status,
        ], 200);
    }

    public function FunctionName() : Returntype {
         // Designacion de asesor
         $sol_da = Solicitude::where('student_id', $student_id)->first();
         $off_da = DocOf::where('solicitude_id', $sol_da->_id)->first();
         $res_da = DocResolution::where('docof_id', $off_da->_id)->first();
 
         $revision_presidente = Review::where('student_id', $student_id)->where('rev_adviser_rol', 'presidente')->where('rev_type', 'informe')->first();
         $revision_secretario = Review::where('student_id', $student_id)->where('rev_adviser_rol', 'secretario')->where('rev_type', 'informe')->first();
         $revision_vocal = Review::where('student_id', $student_id)->where('rev_adviser_rol', 'vocal')->where('rev_type', 'informe')->first();
         $revision_asesor = Review::where('student_id', $student_id)->where('rev_adviser_rol', 'asesor')->where('rev_type', 'informe')->first();
 
         if($revision_presidente and $revision_secretario and $revision_vocal and $revision_asesor){
                 
             $presidente = Adviser::where('_id', $revision_presidente->adviser_id)->first();
             $presidente = ucwords(strtolower($presidente->adv_name . ' ' . $presidente->adv_lastname_m . ' ' . $presidente->adv_lastname_f));
 
             $secretario = Adviser::where('_id', $revision_secretario->adviser_id)->first();
             $secretario = ucwords(strtolower($secretario->adv_name . ' ' . $secretario->adv_lastname_m . ' ' . $secretario->adv_lastname_f));
 
             $vocal = Adviser::where('_id', $revision_vocal->adviser_id)->first();
             $vocal = ucwords(strtolower($vocal->adv_name . ' ' . $vocal->adv_lastname_m . ' ' . $vocal->adv_lastname_f));
 
             $asesor = Adviser::where('_id', $revision_asesor->adviser_id)->first();
             $asesor = ucwords(strtolower($asesor->adv_name . ' ' . $asesor->adv_lastname_m . ' ' . $asesor->adv_lastname_f));
         }
 
         $res_emisor = 'Facultad de ingeniería';
         

         '1' => [
                'doc_nombre' => 'Resolución de designación de asesor',
                'doc_emisor' => $res_emisor ?? '',
                'doc_fecha' => Carbon::parse($res_da->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY'),
                'doc_id' => $res_da->_id ?? '',

            ],
            '2' => [
                'doc_nombre' => 'Acta de conformidad de informe final - Asesor',
                'doc_emisor' => $asesor ?? '',
                'doc_fecha' => Carbon::parse($revision_asesor->updated_at ?? '')->locale('es')->isoFormat('DD[-]MM[-]YYYY'),
                'doc_id' => $revision_asesor->_id ?? '',
            ],
            '3' => [
                'nombre_doc' => 'Acta de conformidad de informe final - Presidente' ?? '',
                'doc_emisor' => $presidente ?? '',
                'doc_fecha' => Carbon::parse($revision_presidente->updated_at ?? '')->locale('es')->isoFormat('DD[-]MM[-]YYYY'),
                'doc_id' => $revision_presidente->_id ?? '',
            ],
            '4' => [
                'nombre_doc' => 'Acta de conformidad de informe final - Secretario' ?? '',
                'doc_emisor' => $secretario ?? '',
                'doc_fecha' => Carbon::parse($revision_secretario->updated_at ?? '')->locale('es')->isoFormat('DD[-]MM[-]YYYY'),
                'doc_id' => $revision_secretario->_id ?? '',
            ],
            '5' => [
                'nombre_doc' => 'Acta de conformidad de informe final - Vocal' ?? '',
                'doc_emisor' => $vocal ?? '',
                'doc_fecha' => Carbon::parse($revision_vocal->updated_at ?? '')->locale('es')->isoFormat('DD[-]MM[-]YYYY'),
                'doc_id' => $revision_vocal->_id ?? '',
            ],
    }
}

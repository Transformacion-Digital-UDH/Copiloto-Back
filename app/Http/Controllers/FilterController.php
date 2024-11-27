<?php

namespace App\Http\Controllers;

use App\Models\Adviser;
use App\Models\DocOf;
use App\Models\DocResolution;
use App\Models\Filter;
use App\Models\Review;
use App\Models\Solicitude;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FilterController extends Controller
{
    public function createReviewVRI($student_id)
    {
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

        // Verificar si ya existe un registro en "primer filtro"
        $existe = Filter::where('student_id', $student_id)
            ->where('fil_name', 'primer filtro')
            ->exists(); // Utilizar exists() para verificar directamente si existe

        if ($existe) {
            return response()->json([
                'mensaje' => 'El estudiante ya tiene una revisión en proceso en el primer filtro',
            ], 400); // Añadir return para detener la ejecución
        }

        // Crear nueva solicitud de aprobación de tesis
        $filter = Filter::create([
            'student_id' => $student_id,
            'fil_name' => 'primer filtro',
            'fil_status' => 'pendiente',
        ]);

        // Guardar el nuevo documento en la base de datos
        return response()->json([
            'mensaje' => 'Revisión en primer filtro creada exitosamente',
            'estado' => $filter->fil_status,
        ], 200);
    }


    public function getStudentsFirstFilter()
    {
        try {
            // Obtener filtros asociados al "primer filtro"
            $filters = Filter::where('fil_name', 'primer filtro')->get();

            $data = [];

            foreach ($filters as $filter) {
                $student_id = $filter->student_id;

                // Obtener datos del estudiante
                $student = Student::where('_id', $student_id)->first();
                $student_name = $student ? ucwords(strtolower($student->stu_name . ' ' . $student->stu_lastname_m . ' ' . $student->stu_lastname_f)) : '';

                // Designación de asesor
                $sol_da = Solicitude::where('student_id', $student_id)->first();
                $off_da = $sol_da ? DocOf::where('solicitude_id', $sol_da->_id)->first() : null;
                $res_da = $off_da ? DocResolution::where('docof_id', $off_da->_id)->first() : null;

                // Revisiones de informe
                $revision_presidente = Review::where('student_id', $student_id)
                    ->where('rev_adviser_rol', 'presidente')
                    ->where('rev_type', 'informe')
                    ->first();

                $revision_secretario = Review::where('student_id', $student_id)
                    ->where('rev_adviser_rol', 'secretario')
                    ->where('rev_type', 'informe')
                    ->first();

                $revision_vocal = Review::where('student_id', $student_id)
                    ->where('rev_adviser_rol', 'vocal')
                    ->where('rev_type', 'informe')
                    ->first();

                $revision_asesor = Review::where('student_id', $student_id)
                    ->where('rev_adviser_rol', 'asesor')
                    ->where('rev_type', 'informe')
                    ->first();

                // Datos de los asesores
                $presidente = $revision_presidente ? Adviser::find($revision_presidente->adviser_id) : null;
                $secretario = $revision_secretario ? Adviser::find($revision_secretario->adviser_id) : null;
                $vocal = $revision_vocal ? Adviser::find($revision_vocal->adviser_id) : null;
                $asesor = $revision_asesor ? Adviser::find($revision_asesor->adviser_id) : null;

                // Nombres en formato adecuado
                $presidente_name = $presidente ? ucwords(strtolower($presidente->adv_name . ' ' . $presidente->adv_lastname_m . ' ' . $presidente->adv_lastname_f)) : '';
                $secretario_name = $secretario ? ucwords(strtolower($secretario->adv_name . ' ' . $secretario->adv_lastname_m . ' ' . $secretario->adv_lastname_f)) : '';
                $vocal_name = $vocal ? ucwords(strtolower($vocal->adv_name . ' ' . $vocal->adv_lastname_m . ' ' . $vocal->adv_lastname_f)) : '';
                $asesor_name = $asesor ? ucwords(strtolower($asesor->adv_name . ' ' . $asesor->adv_lastname_m . ' ' . $asesor->adv_lastname_f)) : '';

                $res_emisor = 'Facultad de Ingeniería';

                // Datos estructurados
                $data[] = [
                    'estudiante_id' => $student_id,
                    'nombre' => $student_name,
                    'filtro_id' => $filter->_id,
                    'filtro_fecha' => $filter ? Carbon::parse($filter->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY') : '',
                    'filtro_estado' => $filter->fil_status,
                    'documentos' => [
                        [
                            'doc_nombre' => 'Resolución de designación de asesor',
                            'doc_emisor' => $res_emisor,
                            'doc_fecha' => $res_da ? Carbon::parse($res_da->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY') : '',
                            'doc_id' => $res_da->_id ?? '',
                        ],
                        [
                            'doc_nombre' => 'Acta de conformidad de informe final - Asesor',
                            'doc_emisor' => $asesor_name,
                            'doc_fecha' => $revision_asesor ? Carbon::parse($revision_asesor->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY') : '',
                            'doc_id' => $revision_asesor->_id ?? '',
                        ],
                        [
                            'doc_nombre' => 'Acta de conformidad de informe final - Presidente',
                            'doc_emisor' => $presidente_name,
                            'doc_fecha' => $revision_presidente ? Carbon::parse($revision_presidente->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY') : '',
                            'doc_id' => $revision_presidente->_id ?? '',
                        ],
                        [
                            'doc_nombre' => 'Acta de conformidad de informe final - Secretario',
                            'doc_emisor' => $secretario_name,
                            'doc_fecha' => $revision_secretario ? Carbon::parse($revision_secretario->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY') : '',
                            'doc_id' => $revision_secretario->_id ?? '',
                        ],
                        [
                            'doc_nombre' => 'Acta de conformidad de informe final - Vocal',
                            'doc_emisor' => $vocal_name,
                            'doc_fecha' => $revision_vocal ? Carbon::parse($revision_vocal->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY') : '',
                            'doc_id' => $revision_vocal->_id ?? '',
                        ],
                    ],
                ];
            }

            // Ordenar datos: primero por estado (pendiente, aprobado), luego por fecha más reciente
            usort($data, function ($a, $b) {
                // Ordenar por estado
                $estadoOrden = ['pendiente' => 1, 'aprobado' => 2];
                $estadoA = $estadoOrden[$a['filtro_estado']] ?? 3;
                $estadoB = $estadoOrden[$b['filtro_estado']] ?? 3;

                if ($estadoA !== $estadoB) {
                    return $estadoA <=> $estadoB;
                }

                // Ordenar por fecha más reciente
                return strtotime($b['filtro_fecha']) <=> strtotime($a['filtro_fecha']);
            });

            // Retornar datos estructurados
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error in getStudentsFirstFilter: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener los datos: ' . $e->getMessage()], 500);
        }
    }

    public function updateFilter(Request $request, $fil_id)
    {
        // Buscar el filtro
        $filter = Filter::where('_id', $fil_id)
                        ->where('fil_status', '!=', 'aprobado')
                        ->first();

        if (!$filter) {
            return response()->json(['message' => 'Registro no encontrado o en proceso.'], 404);
        }

        // Buscar al estudiante asociado
        $student = Student::where('_id', $filter->student_id)->first();
        $student_dni = $student->stu_dni;

        // Validar la solicitud
        $rules = [
            'fil_estado' => 'required|string|in:aprobado',
        ];

        $this->validate($request, $rules);

        // Obtener el estado enviado
        $status = $request->input('fil_estado');

        // Procesar según el estado
        switch ($status) {
            case 'aprobado':

                $fil_name = $filter->fil_name;

                switch ($fil_name) {
                    case 'primer filtro':
                            // Crear el segundo filtro
                        Filter::create([
                            'student_id' => $filter->student_id,
                            'fil_name' => 'segundo filtro',
                            'fil_status' => 'pendiente',
                            'fil_path' => '',
                        ]);

                        // Actualizar el estado del primer filtro
                        $filter->update([
                            'fil_status' => $status,
                        ]);

                        return response()->json([
                            'estado' => 'aprobado',
                            'message' => 'Primer filtro aprobado',
                        ], 200);
                        break;

                    case 'segundo filtro':
                        if ($request->hasFile('fil_path')) {

                            $request->validate([
                                'fil_path' => 'required|file|mimes:pdf|max:10240',
                            ]);
                            // Obtener el nombre del archivo usando el DNI y agregar la extensión .pdf
                            $fileName = 'CBP_' . $student_dni . '.pdf';
                
                            // Guardar el archivo con el nombre especificado en la carpeta 'Constancias'
                            $path = $request->file('fil_path')->storeAs('ConstanciaBuenasPracticas', $fileName, 'public');
                
                            // Actualizar los campos en la base de datos
                            $filter->fil_path = $path; // Ruta del archivo guardado
                            $filter->fil_status = $status; 
                            $filter->save();
           
                                    // Crear el segundo filtro
                            Filter::create([
                                'student_id' => $filter->student_id,
                                'fil_name' => 'tercer filtro',
                                'fil_status' => 'pendiente',
                                'fil_path' => '',
                            ]);

                            return response()->json([
                                'estado' => $status,
                                'message' => 'Archivo subido correctamente. Segundo filtro aprobado',
                            ], 200);
                        }

                        return response()->json([
                            'message' => 'No se pudo cargar el archivo.'
                        ]);

                        break;
                    case 'tercer filtro':

                        if ($request->hasFile('fil_path')) {

                            $request->validate([
                                'fil_path' => 'required|file|mimes:pdf|max:10240',
                            ]);
                            // Obtener el nombre del archivo usando el DNI y agregar la extensión .pdf
                            $fileName = 'CO_' . $student_dni . '.pdf';
                
                            // Guardar el archivo con el nombre especificado en la carpeta 'Constancias'
                            $path = $request->file('fil_path')->storeAs('ConstanciaOriginalidad', $fileName, 'public');
                
                            // Actualizar los campos en la base de datos
                            $filter->fil_path = $path; // Ruta del archivo guardado
                            $filter->fil_status = $status; 
                            $filter->save();

                            return response()->json([
                                'estado' => $status,
                                'message' => 'Archivo subido correctamente. Tercer filtro aprobado',
                            ], 200);
                        }

                        return response()->json([
                            'message' => 'No se pudo cargar el archivo.'
                        ]);
                        break;
                    
                    default:
                        return response()->json(['message' => 'Filtro no válido'], 400);
                    }
                break;


            default:
                return response()->json(['message' => 'Estado no válido.'], 400);
            }
        }

    public function getStudentsSecondFilter(){
        try {
            // Obtener filtros asociados al "primer filtro"
            $filters = Filter::where('fil_name', 'segundo filtro')->get();

            $data = [];

            foreach ($filters as $filter) {
                $student_id = $filter->student_id;

                // Obtener datos del estudiante
                $student = Student::where('_id', $student_id)->first();
                $student_name = $student ? ucwords(strtolower($student->stu_name . ' ' . $student->stu_lastname_m . ' ' . $student->stu_lastname_f)) : '';

                // Designación de asesor
                $sol_da = Solicitude::where('student_id', $student_id)->first();
                $informe = $sol_da->informe_link;

                
                // Datos estructurados
                $data[] = [
                    'estudiante_id' => $student_id,
                    'nombre' => $student_name,
                    'filtro_id' => $filter->_id,
                    'filtro_fecha' => $filter ? Carbon::parse($filter->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY') : '',
                    'filtro_estado' => $filter->fil_status,
                    'documentos' => [
                        [
                            'doc_nombre' => 'Informe final de proyecto de tesis',
                            'doc_link' => $informe ?? '',
                        ],
                    ],
                ];
            }

            // Ordenar datos: primero por estado (pendiente, aprobado), luego por fecha más reciente
            usort($data, function ($a, $b) {
                // Ordenar por estado
                $estadoOrden = ['pendiente' => 1, 'aprobado' => 2];
                $estadoA = $estadoOrden[$a['filtro_estado']] ?? 3;
                $estadoB = $estadoOrden[$b['filtro_estado']] ?? 3;

                if ($estadoA !== $estadoB) {
                    return $estadoA <=> $estadoB;
                }

                // Ordenar por fecha más reciente
                return strtotime($b['filtro_fecha']) <=> strtotime($a['filtro_fecha']);
            });

            // Retornar datos estructurados
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error in getStudentsFirstFilter: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener los datos: ' . $e->getMessage()], 500);
        }

    }

    public function getStudentsTirdFilter(){
        try {
            // Obtener filtros asociados al "primer filtro"
            $filters = Filter::where('fil_name', 'tercer filtro')->get();

            $data = [];

            foreach ($filters as $filter) {
                $student_id = $filter->student_id;

                // Obtener datos del estudiante
                $student = Student::where('_id', $student_id)->first();
                $student_name = $student ? ucwords(strtolower($student->stu_name . ' ' . $student->stu_lastname_m . ' ' . $student->stu_lastname_f)) : '';

                // Designación de asesor
                $sol_da = Solicitude::where('student_id', $student_id)->first();
                $informe = $sol_da->informe_link;


                // Datos estructurados
                $data[] = [
                    'estudiante_id' => $student_id,
                    'nombre' => $student_name,
                    'filtro_id' => $filter->_id,
                    'filtro_fecha' => $filter ? Carbon::parse($filter->updated_at)->locale('es')->isoFormat('DD[-]MM[-]YYYY') : '',
                    'filtro_estado' => $filter->fil_status,
                    'documentos' => [
                        [
                            'doc_nombre' => 'Informe final de proyecto de tesis',
                            'doc_link' => $informe ?? '',
                        ],
                    ],
                ];
            }

            // Ordenar datos: primero por estado (pendiente, aprobado), luego por fecha más reciente
            usort($data, function ($a, $b) {
                // Ordenar por estado
                $estadoOrden = ['pendiente' => 1, 'aprobado' => 2];
                $estadoA = $estadoOrden[$a['filtro_estado']] ?? 3;
                $estadoB = $estadoOrden[$b['filtro_estado']] ?? 3;

                if ($estadoA !== $estadoB) {
                    return $estadoA <=> $estadoB;
                }

                // Ordenar por fecha más reciente
                return strtotime($b['filtro_fecha']) <=> strtotime($a['filtro_fecha']);
            });

            // Retornar datos estructurados
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error in getStudentsFirstFilter: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener los datos: ' . $e->getMessage()], 500);
        }

    }

}



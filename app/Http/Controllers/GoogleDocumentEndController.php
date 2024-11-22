<?php

namespace App\Http\Controllers;

use App\Models\Adviser;
use App\Models\Paisi;
use App\Models\Solicitude;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class GoogleDocumentEndController extends GoogleDocumentController
{
    public function createInforme(Request $request)
    {
        try {
            $solicitudeId = $request->input('solicitude_id');

            $solicitude = Solicitude::where('_id',$solicitudeId)->first();
            $student = Student::where('_id',$solicitude->student_id)->first();
            $adviser = Adviser::where('_id',$solicitude->adviser_id)->first();
            $paisi = Paisi::where('pai_program','INGENIERÍA DE SISTEMAS E INFORMÁTICA')->first();
            $studentUser = User::where('_id',$student->user_id)->first();
            $adviserUser = User::where('_id',$adviser->user_id)->first();
            $paisiUser = User::where('_id',$paisi->user_id)->first();


            // Personaliza el nombre del documento y el ID de la plantilla para el informe
            $documentName = "Informe_" . $solicitude->sol_title_inve . "_" . $student->stu_name;
            $templateId = '1ud0cDToshwGaLxlSO6SvPF63ua0x1akKjZE9MoKvUic';
            $folderId = '1ga2gIsAw5-Kit-oMbBuWavaXtiRunwhz';

            // Crear el documento desde la plantilla específica del informe
            $documentId = $this->createDocumentFromTemplate($templateId, $documentName);
            if (!$documentId) {
                return response()->json(['error' => 'No se pudo crear el documento'], 500);
            }

            // Reemplazar los marcadores de posición
            $this->replaceDocumentPlaceholders($documentId, $solicitude, $student, $adviser);

            // Asignar permisos
            $this->assignPermissions($documentId, $paisiUser, $studentUser, $adviserUser);
            
            // Mover el documento a la carpeta específica del informe
            $this->moveDocumentToFolder($documentId, $folderId);

            // Obtener el enlace del documento y actualizar usando 'informe_link'
            $link = $this->getDocumentLink($documentId);
            if (!$link) {
                return response()->json(['error' => 'No se pudo obtener el enlace del documento'], 500);
            }

            // Usar el nuevo parámetro linkType para especificar que es un informe_link
            $this->updateSolicitudeWithLink($solicitude, $link, 'informe_link');

            return response()->json(['success' => true, 'informe_link' => $link]);

        } catch (\Exception $e) {
            Log::error('Error creating informe: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear el informe: ' . $e->getMessage()], 500);
        }
    }
}
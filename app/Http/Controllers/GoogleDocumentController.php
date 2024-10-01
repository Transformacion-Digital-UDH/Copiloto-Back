<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_Docs;
use Google_Service_Docs_Document;
use Google_Service_Docs_BatchUpdateDocumentRequest;
use Google_Service_Docs_Request;
use Google_Service_Drive;
use Google_Service_Drive_Permission;
use Google_Service_Drive_DriveFile;
use Illuminate\Http\Request;
use App\Models\Solicitude;
use App\Models\Student;
use App\Models\Adviser;

class GoogleDocumentController extends Controller
{
    protected $client;
    protected $driveService;
    protected $docsService;

    public function __construct()
    {
        // Cargar las credenciales de la cuenta de servicio
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path('app/google/proyectotitulacionudh-72441794cf5a.json'));

        $this->client = new Google_Client();
        $this->client->useApplicationDefaultCredentials();
        $this->client->addScope(Google_Service_Drive::DRIVE);
        $this->client->addScope(Google_Service_Docs::DOCUMENTS);

        // Inicializar los servicios de Drive y Docs
        $this->driveService = new Google_Service_Drive($this->client);
        $this->docsService = new Google_Service_Docs($this->client);
    }

    public function createDocument(Request $request)
    {
        $solicitudeId = $request->input('solicitude_id');
        $templateId = '1772-BG2ADrPsFJxSMNr3rOxjw9VDROeIbtveWPvXp0g'; // ID de la plantilla en Google Drive
        $defaultOwnerEmail = 'paisi.udh@gmail.com'; // el editor predeterminado

        // Obtener los datos de la solicitud y los usuarios
        $solicitude = Solicitude::find($solicitudeId);
        $student = Student::find($solicitude->student_id);
        $adviser = Adviser::find($solicitude->adviser_id);

        if (!$solicitude || !$student || !$adviser) {
            return response()->json(['error' => 'Solicitud, estudiante o asesor no encontrado'], 404);
        }

        // Crear el nombre del documento con el ID de la solicitud o el nombre del estudiante
        $documentName = "Documento_Tesis_" . $solicitude->sol_title_inve ."_". $student->stu_name;

        // 1. Hacer una copia de la plantilla en Google Drive
        $copy = new Google_Service_Drive_DriveFile([
            'name' => $documentName
        ]);

        try {
            // Copiar el archivo plantilla
            $copiedFile = $this->driveService->files->copy($templateId, $copy);
            $documentId = $copiedFile->getId();

            if (!$documentId) {
                return response()->json(['error' => 'No se pudo crear el documento'], 500);
            }

            // 2. Reemplazar los marcadores de posición (placeholders) en el documento copiado
            $requests = [
                new Google_Service_Docs_Request([
                    'replaceAllText' => [
                        'containsText' => [
                            'text' => '{{student_full_name}}', 
                            'matchCase' => true
                        ],
                        'replaceText' => $student->stu_name . ' ' . $student->stu_lastname_m . ' ' . $student->stu_lastname_f
                    ]
                ]),
                new Google_Service_Docs_Request([
                    'replaceAllText' => [
                        'containsText' => [
                            'text' => '{{adviser_full_name}}',
                            'matchCase' => true
                        ],
                        'replaceText' => $adviser->adv_name . ' ' . $adviser->adv_lastname_m . ' ' . $adviser->adv_lastname_f
                    ]
                ]),
                new Google_Service_Docs_Request([
                    'replaceAllText' => [
                        'containsText' => [
                            'text' => '{{thesis_title}}',
                            'matchCase' => true
                        ],
                        'replaceText' => $solicitude->sol_title_inve
                    ]
                ])
            ];

            $batchUpdateRequest = new Google_Service_Docs_BatchUpdateDocumentRequest([
                'requests' => $requests
            ]);

            // Actualizamos el documento con los nuevos valores
            $this->docsService->documents->batchUpdate($documentId, $batchUpdateRequest);

            // 3. Asignar permisos de escritura al editor predeterminado
            $editorPermission = new Google_Service_Drive_Permission();
            $editorPermission->setType('user');
            $editorPermission->setRole('writer');
            $editorPermission->setEmailAddress($defaultOwnerEmail);

            $this->driveService->permissions->create($documentId, $editorPermission);

            // Mover documento a una carpeta del drive
            $folderId = '1Diiq8CbTzB5EdXvZCJnEEq4MEMOUiA8I'; // Reemplaza con el folderId correcto
            $emptyFileMetadata = new Google_Service_Drive_DriveFile();
            $this->driveService->files->update($documentId, $emptyFileMetadata, [
                'addParents' => $folderId,
                'removeParents' => 'root',
                'fields' => 'id, parents'
            ]);

            // Obtener el enlace de visualización desde Google Drive
            $file = $this->driveService->files->get($documentId, ['fields' => 'webViewLink']);
            $link = $file->getWebViewLink();

            if (!$link) {
                return response()->json(['error' => 'No se pudo obtener el enlace del documento'], 500);
            }

            // Actualizar la solicitud con el enlace del documento
            $solicitude->document_link = $link;
            $solicitude->save();

            return response()->json(['link' => $link]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    /*public function createDocument(Request $request)
    {
        $solicitudeId = $request->input('solicitude_id');
        $title = "Documento para Solicitud $solicitudeId";
        $defaultOwnerEmail = 'paisi.udh@gmail.com'; // el editor predeterminado

        // Crear el documento en Google Docs
        $document = new Google_Service_Docs_Document([
            'title' => $title
        ]);

        try {
            // Crear el documento en Google Docs
            $createdDocument = $this->docsService->documents->create($document);
            $documentId = $createdDocument->getDocumentId();

            if (!$documentId) {
                return response()->json(['error' => 'No se pudo crear el documento'], 500);
            }

            // Asignar permisos de escritura al propietario predeterminado
            $editorPermission = new Google_Service_Drive_Permission();
            $editorPermission->setType('user');
            $editorPermission->setRole('writer'); // Rol de editor
            $editorPermission->setEmailAddress($defaultOwnerEmail);

            $this->driveService->permissions->create($documentId, $editorPermission);

            // Mover documento a una carpeta del drive
            $folderId = '1Diiq8CbTzB5EdXvZCJnEEq4MEMOUiA8I'; // Reemplaza con el folderId
            $emptyFileMetadata = new Google_Service_Drive_DriveFile();
            $this->driveService->files->update($documentId, $emptyFileMetadata, [
                'addParents' => $folderId,
                'removeParents' => 'root',
                'fields' => 'id, parents'
            ]);

            // Obtener el enlace de visualización desde Google Drive
            $file = $this->driveService->files->get($documentId, ['fields' => 'webViewLink']);
            $link = $file->getWebViewLink();

            if (!$link) {
                return response()->json(['error' => 'No se pudo obtener el enlace del documento'], 500);
            }

            // Actualizar la solicitud con el enlace del documento
            $solicitude = Solicitude::find($solicitudeId);
            if ($solicitude) {
                $solicitude->document_link = $link;
                $solicitude->save();
            } else {
                return response()->json(['error' => 'Solicitud no encontrada'], 404);
            }

            return response()->json(['link' => $link]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }*/

    public function getDocumentLink($solicitudeId)
    {
        // Buscar la solicitud por su ID
        $solicitude = Solicitude::find($solicitudeId);

        if (!$solicitude) {
            return response()->json(['error' => 'Solicitud no encontrada'], 404);
        }

        // Verificar si la solicitud tiene un enlace de documento
        if (!$solicitude->document_link) {
            return response()->json(['error' => 'No hay documento asociado a esta solicitud'], 404);
        }

        // Retornar el enlace del documento
        return response()->json(['document_link' => $solicitude->document_link], 200);
    }
}

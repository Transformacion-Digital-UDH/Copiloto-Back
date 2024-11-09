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
use App\Models\Paisi;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class GoogleDocumentController extends Controller
{
    protected $client;
    protected $driveService;
    protected $docsService;

    public function __construct()
    {
        $this->initializeGoogleClient();
    }

    protected function initializeGoogleClient()
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

    /**
     * API endpoint to create a Google Doc
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createDocument(Request $request)
    {
        try {
            $solicitudeId = $request->input('solicitude_id');
            
            // Obtener los datos de la solicitud y los usuarios
            $data = $this->validateAndGetData($solicitudeId);
            if (!$data) {
                return response()->json(['error' => 'Datos no encontrados'], 404);
            }

            $solicitude = $data['solicitude'];
            $student = $data['student'];
            $adviser = $data['adviser'];
            $paisi = $data['paisi'];
            $studentUser = $data['studentUser'];
            $adviserUser = $data['adviserUser'];
            $paisiUser = $data['paisiUser'];

            // Crear el nombre del documento
            $documentName = $this->generateDocumentName($solicitude, $student);

            // Obtener el ID de la plantilla
            $templateId = $this->getTemplateId($solicitude->sol_type_inve);
            if (!$templateId) {
                return response()->json(['error' => 'Tipo de investigación no válido o plantilla no disponible'], 400);
            }

            // Crear el documento
            $documentId = $this->createDocumentFromTemplate($templateId, $documentName);
            if (!$documentId) {
                return response()->json(['error' => 'No se pudo crear el documento'], 500);
            }

            // Reemplazar los marcadores de posición
            $this->replaceDocumentPlaceholders($documentId, $solicitude, $student, $adviser);

            // Asignar permisos
            $this->assignPermissions($documentId, $paisiUser, $studentUser, $adviserUser);

            // Mover el documento a una carpeta específica
            $this->moveDocumentToFolder($documentId);

            // Obtener el enlace del documento
            $link = $this->getDocumentLink($documentId);
            if (!$link) {
                return response()->json(['error' => 'No se pudo obtener el enlace del documento'], 500);
            }

            // Actualizar la solicitud con el enlace del documento
            $this->updateSolicitudeWithLink($solicitude, $link);

            return response()->json(['success' => true, 'link' => $link]);

        } catch (\Exception $e) {
            Log::error('Error creating document: ' . $e->getMessage());
            return response()->json(['error' => 'Error al crear el documento: ' . $e->getMessage()], 500);
        }
    }

    protected function validateAndGetData($solicitudeId)
    {
        $solicitude = Solicitude::find($solicitudeId);
        if (!$solicitude) {
            return null;
        }

        $student = Student::find($solicitude->student_id);
        $adviser = Adviser::find($solicitude->adviser_id);
        $paisi = Paisi::find($solicitude->paisi_id);

        if (!$student || !$adviser || !$paisi) {
            return null;
        }

        $studentUser = User::find($student->user_id);
        $adviserUser = User::find($adviser->user_id);
        $paisiUser = User::find($paisi->user_id);

        if (!$studentUser || !$adviserUser || !$paisiUser) {
            return null;
        }

        return [
            'solicitude' => $solicitude,
            'student' => $student,
            'adviser' => $adviser,
            'paisi' => $paisi,
            'studentUser' => $studentUser,
            'adviserUser' => $adviserUser,
            'paisiUser' => $paisiUser
        ];
    }

    protected function generateDocumentName($solicitude, $student)
    {
        return "Documento_Tesis_" . $solicitude->sol_title_inve . "_" . $student->stu_name;
    }

    protected function getTemplateId($investigationType)
    {
        $templates = [
            'cientifica' => '1772-BG2ADrPsFJxSMNr3rOxjw9VDROeIbtveWPvXp0g',
            'tecnologica' => '1SoVMrPlTg0Rswo1qU-vCXMTQSbel0kvtrGQehPPyfyw'
        ];

        return isset($templates[$investigationType]) ? $templates[$investigationType] : null;
    }

    protected function createDocumentFromTemplate($templateId, $documentName)
    {
        $copy = new Google_Service_Drive_DriveFile([
            'name' => $documentName
        ]);

        $copiedFile = $this->driveService->files->copy($templateId, $copy);
        return $copiedFile->getId();
    }

    protected function replaceDocumentPlaceholders($documentId, $solicitude, $student, $adviser)
    {
        $requests = [
            $this->createReplaceTextRequest('{{student_full_name}}', $student->stu_name . ' ' . $student->stu_lastname_m . ' ' . $student->stu_lastname_f),
            $this->createReplaceTextRequest('{{adviser_full_name}}', $adviser->adv_name . ' ' . $adviser->adv_lastname_m . ' ' . $adviser->adv_lastname_f),
            $this->createReplaceTextRequest('{{thesis_title}}', $solicitude->sol_title_inve)
        ];

        $batchUpdateRequest = new Google_Service_Docs_BatchUpdateDocumentRequest([
            'requests' => $requests
        ]);

        $this->docsService->documents->batchUpdate($documentId, $batchUpdateRequest);
    }

    protected function createReplaceTextRequest($placeholder, $replaceText)
    {
        return new Google_Service_Docs_Request([
            'replaceAllText' => [
                'containsText' => [
                    'text' => $placeholder,
                    'matchCase' => true
                ],
                'replaceText' => $replaceText
            ]
        ]);
    }

    protected function assignPermissions($documentId, $paisiUser, $studentUser, $adviserUser)
    {
        $this->assignDrivePermissions($documentId, $paisiUser->email, 'writer');
        $this->assignDrivePermissions($documentId, $studentUser->email, 'writer');
        $this->assignDrivePermissions($documentId, $adviserUser->email, 'commenter');
    }

    public function assignDrivePermissions($documentId, $email, $role)
    {
        $permission = new Google_Service_Drive_Permission();
        $permission->setType('user');
        $permission->setRole($role);
        $permission->setEmailAddress($email);

        $this->driveService->permissions->create($documentId, $permission);
    }

    protected function moveDocumentToFolder($documentId, $folderId = '1Diiq8CbTzB5EdXvZCJnEEq4MEMOUiA8I')
    {
        // Usar el $folderId proporcionado o el valor por defecto
        $emptyFileMetadata = new Google_Service_Drive_DriveFile();
        $this->driveService->files->update($documentId, $emptyFileMetadata, [
            'addParents' => $folderId,
            'removeParents' => 'root',
            'fields' => 'id, parents'
        ]);
    }

    protected function getDocumentLink($documentId)
    {
        $file = $this->driveService->files->get($documentId, ['fields' => 'webViewLink']);
        return $file->getWebViewLink();
    }

    protected function updateSolicitudeWithLink($solicitude, $link, $linkType = 'document_link')
    {
        if (!in_array($linkType, ['document_link', 'informe_link'])) {
            $linkType = 'document_link'; // valor por defecto si no es válido
        }
        
        $solicitude->{$linkType} = $link;
        $solicitude->save();
    }

}

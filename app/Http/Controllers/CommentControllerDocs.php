<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_Docs;
use Google_Service_Drive;
use Illuminate\Http\Request;
use App\Models\Solicitude;
use App\Models\Comment;
use Illuminate\Support\Facades\Log;

class CommentControllerDocs extends Controller
{
    protected $client;
    protected $driveService;

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
        // Cambiamos la constante no definida por permisos válidos
        $this->client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
        $this->client->addScope(Google_Service_Docs::DOCUMENTS_READONLY);
        // Se eliminó la constante DRIVE_COMMENTS_READONLY
        // Se puede utilizar el alcance DRIVE en su lugar si se desea acceso general a archivos
        $this->client->addScope(Google_Service_Drive::DRIVE); // Comentado: descomentar si es necesario

        // Inicializar el servicio de Drive
        $this->driveService = new Google_Service_Drive($this->client);
    }

    public function extractAndSaveComments($solicitudeId)
    {
        // Encuentra la solicitud
        $solicitude = Solicitude::find($solicitudeId);
        if (!$solicitude) {
            return response()->json(['success' => false, 'message' => 'Solicitud no encontrada'], 404);
        }
    
        // Obtener el ID del documento a partir del enlace almacenado
        $documentId = $this->getDocumentIdFromLink($solicitude->document_link);
    
        try {
            // Obtener los comentarios del documento, especificando los campos requeridos
            $optParams = [
                'fields' => 'comments(author(displayName), content, createdTime, replies)'
            ];
            $comments = $this->driveService->comments->listComments($documentId, $optParams);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener los comentarios: ' . $e->getMessage()], 500);
        }
    
        // Preparar los comentarios para la base de datos
        $commentsArray = [];
        // Accede a los comentarios a través de la propiedad 'comments'
        foreach ($comments->getComments() as $comment) {  // Cambia getItems() por getComments()
            $commentData = [
                'author' => $comment->getAuthor()->getDisplayName(),
                'content' => $comment->getContent(),
                'created_time' => $comment->getCreatedTime(),
                'replies' => []
            ];
    
            // Añadir respuestas a los comentarios
            foreach ($comment->getReplies() as $reply) {
                $commentData['replies'][] = [
                    'author' => $reply->getAuthor()->getDisplayName(),
                    'content' => $reply->getContent(),
                    'created_time' => $reply->getCreatedTime()
                ];
            }
    
            $commentsArray[] = $commentData;
        }
    
        // Guardar los comentarios en la base de datos en una nueva colección
        $commentRecord = new Comment([
            'solicitude_id' => $solicitudeId,
            'document_id' => $documentId,
            'comments' => $commentsArray
        ]);
    
        $commentRecord->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Comentarios extraídos y guardados exitosamente',
            'comments' => $commentsArray
        ]);
    }
    
    
    // Extrae el ID del documento de Google Docs a partir del enlace
    private function getDocumentIdFromLink($documentLink)
    {
        preg_match('/\/d\/(.+)\//', $documentLink, $matches);
        return $matches[1] ?? null;
    }
}

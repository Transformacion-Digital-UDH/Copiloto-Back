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
                'fields' => 'comments(author(displayName), content, createdTime, id, replies(id, author(displayName), content, createdTime))'
            ];
            $comments = $this->driveService->comments->listComments($documentId, $optParams);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener los comentarios: ' . $e->getMessage()], 500);
        }
    
        // Buscar si ya existe un registro de comentarios para la solicitud
        $existingComments = Comment::where('solicitude_id', $solicitudeId)->first();
    
        // Obtener los IDs de los comentarios ya guardados
        $existingCommentIds = [];
        $existingReplyIds = []; // Almacenaremos también los IDs de las respuestas existentes
        if ($existingComments) {
            $existingCommentsArray = $existingComments->comments; // Convertir a array modificable
            foreach ($existingCommentsArray as $existingComment) {
                $existingCommentIds[] = $existingComment['id'];
    
                // Obtener los IDs de las respuestas ya guardadas
                if (isset($existingComment['replies'])) {
                    foreach ($existingComment['replies'] as $existingReply) {
                        $existingReplyIds[] = $existingReply['id'];
                    }
                }
            }
        }
    
        // Preparar los nuevos comentarios y respuestas (que no estén ya en la base de datos)
        $newCommentsArray = [];
        foreach ($comments->getComments() as $comment) {
            $commentData = [
                'id' => $comment->getId(),
                'author' => $comment->getAuthor()->getDisplayName(),
                'content' => $comment->getContent(),
                'created_time' => $comment->getCreatedTime(),
                'replies' => []
            ];
    
            // Añadir respuestas nuevas a los comentarios
            if ($comment->getReplies()) {
                foreach ($comment->getReplies() as $reply) {
                    // Solo añadir la respuesta si no está ya guardada
                    if (!in_array($reply->getId(), $existingReplyIds)) {
                        $commentData['replies'][] = [
                            'id' => $reply->getId(),
                            'author' => $reply->getAuthor()->getDisplayName(),
                            'content' => $reply->getContent(),
                            'created_time' => $reply->getCreatedTime()
                        ];
                    }
                }
            }
    
            // Añadir solo los nuevos comentarios
            if (!in_array($comment->getId(), $existingCommentIds)) {
                $newCommentsArray[] = $commentData;
            } else {
                // Si el comentario ya existe, solo agregar las nuevas respuestas
                foreach ($existingCommentsArray as &$existingComment) {
                    if ($existingComment['id'] == $comment->getId()) {
                        // Agregar solo las respuestas nuevas
                        if (!empty($commentData['replies'])) {
                            $existingComment['replies'] = array_merge($existingComment['replies'], $commentData['replies']);
                        }
                    }
                }
            }
        }
    
        // Actualizar si ya existen comentarios
        if ($existingComments) {
            // Verificar si hay nuevos comentarios o respuestas para añadir
            if (!empty($newCommentsArray)) {
                // Añadir los nuevos comentarios al array de comentarios existentes
                $existingCommentsArray = array_merge($existingCommentsArray, $newCommentsArray);
            }
    
            // Asignar el array actualizado al modelo antes de guardarlo
            $existingComments->comments = $existingCommentsArray;
            $existingComments->save();
    
            return response()->json([
                'success' => true,
                'message' => 'Comentarios y respuestas actualizados exitosamente',
                'comments' => $existingComments->comments
            ]);
        } else {
            // Si no existe, crear un nuevo registro
            $commentRecord = new Comment([
                'solicitude_id' => $solicitudeId,
                'document_id' => $documentId,
                'comments' => $newCommentsArray
            ]);
    
            $commentRecord->save();
    
            return response()->json([
                'success' => true,
                'message' => 'Comentarios extraídos y guardados exitosamente',
                'comments' => $newCommentsArray
            ]);
        }
    }    

    // Extrae el ID del documento de Google Docs a partir del enlace
    private function getDocumentIdFromLink($documentLink)
    {
        preg_match('/\/d\/(.+)\//', $documentLink, $matches);
        return $matches[1] ?? null;
    }
}

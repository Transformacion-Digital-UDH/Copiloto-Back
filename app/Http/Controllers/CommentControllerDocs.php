<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_Docs;
use Google_Service_Drive;
use Illuminate\Http\Request;
use App\Models\Solicitude;
use App\Models\Comment;
use App\Models\Student;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CommentControllerDocs extends Controller
{
    protected $client;
    protected $driveService;
    protected const COMMENTS_PER_PAGE = 100;

    public function __construct()
    {
        $this->initializeGoogleClient();
    }

    protected function initializeGoogleClient()
    {
        try {
            $credentialsPath = storage_path('app/google/proyectotitulacionudh-72441794cf5a.json');
            
            if (!file_exists($credentialsPath)) {
                throw new \Exception('Google credentials file not found');
            }

            putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credentialsPath);

            $this->client = new Google_Client();
            $this->client->useApplicationDefaultCredentials();
            $this->client->addScope([
                Google_Service_Drive::DRIVE_METADATA_READONLY,
                Google_Service_Docs::DOCUMENTS_READONLY,
                Google_Service_Drive::DRIVE
            ]);

            $this->driveService = new Google_Service_Drive($this->client);
        } catch (\Exception $e) {
            Log::error('Failed to initialize Google client: ' . $e->getMessage());
            throw $e;
        }
    }

    public function extractAndSaveComments($solicitudeId)
    {
        Log::info("Starting comment extraction for solicitude ID: {$solicitudeId}");

        try {
            // Validación de entrada
            $validator = Validator::make(['solicitude_id' => $solicitudeId], [
                'solicitude_id' => 'required|string|exists:solicitudes,_id'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()], 400);
            }

            $solicitude = Solicitude::findOrFail($solicitudeId);
            $documentId = $this->getDocumentIdFromLink($solicitude->document_link);

            if (!$documentId) {
                Log::error("Invalid document link for solicitude ID: {$solicitudeId}");
                return response()->json(['success' => false, 'message' => 'Invalid document link'], 400);
            }

            $allComments = $this->fetchAllComments($documentId);

            $existingComments = Comment::where('solicitude_id', $solicitudeId)->first();
            $newCommentsArray = $this->processComments($allComments, $existingComments);

            if ($existingComments) {
                Log::info("Updating existing comments for solicitude ID: {$solicitudeId}");
                $this->updateExistingComments($existingComments, $newCommentsArray);
                $message = 'Comentarios y respuestas actualizados exitosamente';
            } else {
                Log::info("Creating new comments for solicitude ID: {$solicitudeId}");
                $this->createNewComments($solicitudeId, $documentId, $newCommentsArray);
                $message = 'Comentarios extraídos y guardados exitosamente';
            }

            Log::info("Comment extraction completed for solicitude ID: {$solicitudeId}");
            return response()->json([
                'success' => true,
                'message' => $message,
                'comments' => $newCommentsArray
            ]);

        } catch (\Google_Service_Exception $e) {
            Log::error("Google API error: " . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al acceder a la API de Google: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::error("Error in comment extraction: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return response()->json([
                'success' => false, 
                'message' => 'Error al procesar los comentarios: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function fetchAllComments($documentId)
    {
        $allComments = [];
        $pageToken = null;

        do {
            $optParams = [
                'fields' => 'comments(author(displayName),content,createdTime,id,resolved,replies(id,author(displayName),content,createdTime)),nextPageToken',
                'pageSize' => self::COMMENTS_PER_PAGE,
                'pageToken' => $pageToken
            ];

            $response = $this->driveService->comments->listComments($documentId, $optParams);
            $allComments = array_merge($allComments, $response->getComments());
            $pageToken = $response->getNextPageToken();
        } while ($pageToken != null);

        return $allComments;
    }

    private function processComments($comments, $existingComments)
    {
        $newCommentsArray = [];
        $processedIds = [];
    
        foreach ($comments as $comment) {
            $commentId = $comment->getId();
            
            if (in_array($commentId, $processedIds)) {
                continue;
            }
            
            $processedIds[] = $commentId;
            
            $commentData = $this->formatCommentData($comment);
            
            if (!$existingComments || !$this->commentExists($existingComments, $commentId)) {
                $newCommentsArray[] = $commentData;
            } else {
                $updatedComment = $this->updateExistingComment($existingComments, $commentData);
                // Solo añadir el comentario actualizado si hubo cambios
                if (!empty($updatedComment) && $updatedComment !== true) {
                    $newCommentsArray[] = $updatedComment;
                }
            }
        }
    
        return $newCommentsArray;
    }

    private function commentExists($existingComments, $commentId)
    {
        foreach ($existingComments->getComments() as $comment) {
            if ($comment['id'] === $commentId) {
                return true;
            }
        }
        return false;
    }

    private function formatCommentData($comment)
    {
        $commentData = [
            'id' => $comment->getId(),
            'author' => $comment->getAuthor()->getDisplayName(),
            'content' => $comment->getContent(),
            'created_time' => Carbon::parse($comment->getCreatedTime())->toIso8601String(),
            'resolved' => $comment->getResolved() ?? false,
            'status_history' => [],
            'replies' => []
        ];
    
        if ($comment->getReplies()) {
            foreach ($comment->getReplies() as $reply) {
                $commentData['replies'][] = [
                    'id' => $reply->getId(),
                    'author' => $reply->getAuthor()->getDisplayName(),
                    'content' => $reply->getContent(),
                    'created_time' => Carbon::parse($reply->getCreatedTime())->toIso8601String(),
                ];
            }
        }
    
        return $commentData;
    }   
    
    private function updateExistingComment($existingComments, $newCommentData)
    {
        $existingComment = collect($existingComments->getComments())->first(function ($comment) use ($newCommentData) {
            return $comment['id'] === $newCommentData['id'];
        });

        if ($existingComment) {
            $updated = false;
            
            // Verificar si el estado de resolución ha cambiado
            if ($existingComment['resolved'] !== $newCommentData['resolved']) {
                $updated = true;
                $action = $newCommentData['resolved'] ? 'resolved' : 'reopened';
                
                // Inicializar status_history si no existe
                if (!isset($existingComment['status_history'])) {
                    $existingComment['status_history'] = [];
                }
                
                // Añadir nueva entrada al historial
                $existingComment['status_history'][] = [
                    'action' => $action,
                    'timestamp' => now()->toIso8601String(),
                    'by' => $newCommentData['author'] // Opcional: registrar quién realizó la acción
                ];
                
                // Actualizar el estado resolved
                $existingComment['resolved'] = $newCommentData['resolved'];
            }

            // Actualizar contenido si ha cambiado y no está vacío
            if (!empty($newCommentData['content']) && $existingComment['content'] !== $newCommentData['content']) {
                $existingComment['content'] = $newCommentData['content'];
                $updated = true;
            }

            // Procesar las respuestas
            if (!empty($newCommentData['replies'])) {
                foreach ($newCommentData['replies'] as $newReply) {
                    $existingReply = collect($existingComment['replies'])->first(function ($reply) use ($newReply) {
                        return $reply['id'] === $newReply['id'];
                    });

                    if (!$existingReply) {
                        $existingComment['replies'][] = $newReply;
                        $updated = true;
                    } elseif ($existingReply['content'] !== $newReply['content']) {
                        $existingReplyIndex = array_search($existingReply, $existingComment['replies']);
                        $existingComment['replies'][$existingReplyIndex] = $newReply;
                        $updated = true;
                    }
                }
            }

            if ($updated) {
                $existingComments->updateComment($newCommentData['id'], $existingComment);
                return $existingComment;
            }
        }

        return $updated ? true : null;
    }

    private function updateExistingComments($existingComments, $newCommentsArray)
    {
        foreach ($newCommentsArray as $newComment) {
            if (!$this->commentExists($existingComments, $newComment['id'])) {
                $existingComments->addComment($newComment);
            }
        }
        $existingComments->save();
    }

    private function createNewComments($solicitudeId, $documentId, $newCommentsArray)
    {
        $comment = new Comment([
            'solicitude_id' => $solicitudeId,
            'document_id' => $documentId,
        ]);
        $comment->setComments($newCommentsArray);
        $comment->save();
    }

    private function getDocumentIdFromLink($documentLink)
    {
        if (preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $documentLink, $matches)) {
            return $matches[1];
        }
        return null;
    }
    
    public function getComment($student_id){
        
        $solicitud = Solicitude::where('student_id', $student_id)->first();
        $student = Student::where('_id', $student_id)->first();
        $student_name = ucwords(strtolower($student->stu_lastname_m . ' ' . $student->stu_lastname_f . ', ' . $student->stu_name));

        $comments = Comment::where('solicitude_id',$solicitud->_id)->first();

        return response()->json([
            'est_id' => $student_id,
            'est_nombre' => $student_name,
            'comentarios' => $comments,
        ], 200);
    }
}
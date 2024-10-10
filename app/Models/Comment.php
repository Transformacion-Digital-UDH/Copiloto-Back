<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'comments';

    protected $fillable = [
        'solicitude_id', 
        'document_id', 
        'comments',
    ];

    public function setComments(array $comments)
    {
        $this->attributes['comments'] = $comments;
    }

    public function getComments(): array
    {
        return $this->attributes['comments'] ?? [];
    }

    public function addComment($commentData)
    {
        $comments = $this->getComments();
        $comments[] = $commentData;
        $this->setComments($comments);
    }

    public function updateComment($commentId, $newData)
    {
        $comments = $this->getComments();
        $updated = false;
        foreach ($comments as $key => $comment) {
            if ($comment['id'] === $commentId) {
                // Asegurarse de que status_history existe
                if (!isset($comments[$key]['status_history'])) {
                    $comments[$key]['status_history'] = [];
                }
                
                // Si el estado de resolución ha cambiado
                if (isset($newData['resolved']) && $comment['resolved'] !== $newData['resolved']) {
                    $comments[$key]['status_history'][] = [
                        'action' => $newData['resolved'] ? 'resolved' : 'reopened',
                        'timestamp' => now()->toIso8601String(),
                        'by' => $newData['author'] ?? 'Unknown'
                    ];
                    $comments[$key]['resolved'] = $newData['resolved'];
                }
                
                // Actualizar otros campos
                $comments[$key] = array_merge($comment, $newData);
                $updated = true;
                break;
            }
        }
        if (!$updated) {
            $comments[] = $newData;
        }
        $this->setComments($comments);
    }
}
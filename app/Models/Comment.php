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

    // Eliminamos el cast de 'comments'
    protected $casts = [];

    public function solicitude(): BelongsTo
    {
        return $this->belongsTo(Solicitude::class);
    }

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
        foreach ($comments as $key => $comment) {
            if ($comment['id'] === $commentId) {
                $comments[$key] = array_merge($comment, $newData);
                break;
            }
        }
        $this->setComments($comments);
    }
}
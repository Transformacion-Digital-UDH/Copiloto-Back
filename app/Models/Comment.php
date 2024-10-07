<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\HasOne;

class Comment extends Model
{

    protected $connection = 'mongodb';
    protected $collection = 'comments';  // Nombre de la colección en MongoDB

    protected $fillable = [
        'solicitude_id', 
        'document_id', 
        'comments'
    ];
}

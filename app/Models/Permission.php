<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Permission extends Model
{
    protected $collection = 'permissions'; // Nombre de la colección en MongoDB
    protected $fillable = ['name', 'description'];

}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Defense extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'defenses'; // Nombre de la colección en MongoDB


    protected $fillable = [
        "student_id",
        "def_fecha",
        "def_hora",
        "def_status", //pendiente, reprobado, aprobado, emitido
        "def_score",
    ];
}

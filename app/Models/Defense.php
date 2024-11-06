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
        "def_presidente_id",
        "def_presidente_status",
        "def_secretario_id",
        "def_secretario_status",
        "def_vocal_id",
        "def_vocal_status",
        "def_accesitario_id",
        "def_accesitario_status",
        "def_fecha",
        "def_hora",
    ];
}

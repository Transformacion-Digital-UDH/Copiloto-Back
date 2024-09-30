<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'reviews';

    protected $fillable = [
        "adviser_id",
        "student_id",
        "rev_count", // Comienza en 1
        "rev_file", //excel que sube asesor
        "rev_status", // Pendiente || Aprobado || Observado
        "rev_type", //tesis || informe
    ];

}

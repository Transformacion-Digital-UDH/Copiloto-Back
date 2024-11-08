<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class HistoryReview extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'history_reviews';

    protected $fillable = [
        "adviser_id",
        "student_id",
        "rev_num_of", // Numero de oficio con el que se acepta
        "rev_count", // Comienza en 1
        "rev_file", //excel que sube asesor
        "rev_status", // Pendiente || Aprobado || Observado
        "rev_type", //tesis || informe
        "rev_adviser_rol", // asesor, presidente, secretario, vocal
    ];

}

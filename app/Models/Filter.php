<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Filter extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'filters'; // Nombre de la colección en MongoDB
    protected $fillable = [
        'student_id',
        'fil_name', //primer filtro, segundo filtro, tercer filtro
        'fil_status', //pendiente, observado, aprobado
        'fil_file', //pdf
    ];
}

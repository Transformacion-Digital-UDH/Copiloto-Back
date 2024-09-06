<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Advisor extends Model
{
    use HasFactory;

    // Definir la conexión con MongoDB
    protected $connection = 'mongodb';
    
    // Definir la colección
    protected $collection = 'advisor';

    // Los campos que se pueden llenar
    protected $fillable = [
        'name',
        'email',
        'available',
        'specialization',
        'students_assigned'
    ];

    // Por defecto, students_assigned será un array vacío
    protected $attributes = [
        'students_assigned' => []
    ];
}

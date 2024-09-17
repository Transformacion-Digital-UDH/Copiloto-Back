<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Solicitude extends Model
{

    protected $connection = 'mongodb';
    protected $collection = 'solicitudes';
    
    protected $fillable = [
        'sol_title_inve',   // Campo para almacenar el título de la tesis
        'sol_adviser_id',
        'student_id', // ID del estudiante que hace la solicitud
        'sol_status'

    ];

    // Relación con el modelo Student 
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}

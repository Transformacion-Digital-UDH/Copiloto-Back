<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\HasOne;

class Solicitude extends Model
{

    protected $connection = 'mongodb';
    protected $collection = 'solicitudes';
    
    protected $fillable = [
        'sol_title_inve',   // Campo para almacenar el título de la tesis
        'adviser_id',
        'student_id', // ID del estudiante que hace la solicitud
        'sol_status',
        'sol_num',
        'docs_link'

    ];

    // Relación con el modelo Student 
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function adviser()
    {
        return $this->belongsTo(Adviser::class, 'adviser_id');
    }

    public function docof(): HasOne
    {
        return $this->hasOne(DocOf::class);
    }

}

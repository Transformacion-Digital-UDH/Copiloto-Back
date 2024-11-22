<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;
    
    protected $connection = 'mongodb';
    protected $collection = 'students';
    
    protected $fillable = [
        'stu_name',
        'stu_lastname_m',
        'stu_lastname_f',
        'stu_dni',
        'stu_code',
        'user_id',
        'stu_college',
        'stu_faculty',
    ];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con el asesor (otro usuario)
    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    // Relación con solicitude (Muchos - Solicitudes)
    public function solicitude(): BelongsTo
    {
        return $this->belongsTo(Solicitude::class);
    }

    // Relación con DocResolution (Muchos - Documentos Resolucion)
    public function DocResolution(): BelongsTo
    {
        return $this->belongsTo(Solicitude::class);
    }

    // Relación con DoCof (Muchos - Documentos Oficio)
    public function DoCof(): BelongsTo
    {
        return $this->belongsTo(Solicitude::class);
    }

    // Función para obtener el nombre completo
    public function getFullName(){
        return $this->stu_name . ' ' . $this->stu_lastname_m . ' ' . $this->stu_lastname_f;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MongoDB\Laravel\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    
    protected $connection = 'mongodb';
    protected $collection = 'students';
    
    protected $fillable = [
        'stu_name',
        'stu_lastname_m',
        'stu_latsname_f',
        'stu_dni',
        'stu_code',
        'user_id',
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
    public function Solicitude(): BelongsTo
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
}

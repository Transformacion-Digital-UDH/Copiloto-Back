<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;

class Paisi extends Model
{
    use HasFactory;
    
    protected $connection = 'mongodb';
    protected $collection = 'paisis'; // Nombre de la colección en MongoDB
    protected $fillable = [

        'user_id',
        'pai_rank',
        'pai_name',
        'pai_lastname_m',
        'pai_lastname_f',
        'pai_program',
        'pai_faculty',

    ];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con solicitude (Muchos - Solicitudes)
    public function solicitude(): BelongsTo
    {
        return $this->belongsTo(Solicitude::class);
    }

    // Función para obtener el nombre completo
    public function getFullName(){
        return $this->pai_name . ' ' . $this->pai_lastname_m . ' ' . $this->pai_lastname_f;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;

class Program extends Model
{
    use HasFactory;
    
    protected $connection = 'mongodb';
    protected $collection = 'programs'; // Nombre de la colección en MongoDB
    protected $fillable = [

        'user_id',
        'pa_rank',
        'pa_name',
        'pa_lastname_m',
        'pa_lastname_f',
        'pa_program',
        'pa_faculty',

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
        return $this->pa_name . ' ' . $this->pa_lastname_m . ' ' . $this->pa_lastname_f;
    }
}

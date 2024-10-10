<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;

class Adviser extends Model
{
    use HasFactory;
    
    protected $connection = 'mongodb';
    protected $collection = 'advisers'; // Nombre de la colección en MongoDB
    protected $fillable = [
        'adv_rank',
        'adv_name',
        'adv_lastname_m',
        'adv_lastname_f',
        'adv_orcid',
        'user_id',
        'adv_is_jury'
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
        return $this->adv_name . ' ' . $this->adv_lastname_m . ' ' . $this->adv_lastname_f;
    }
}

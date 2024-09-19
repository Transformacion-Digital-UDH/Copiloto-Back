<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Adviser extends Model
{
    use HasFactory;
    
    protected $connection = 'mongodb';
    protected $collection = 'advisers'; // Nombre de la colección en MongoDB
    protected $fillable = [
        'adv_name',
        'adv_lastname_m',
        'adv_latsname_f',
        'adv_orcid',
        'user_id',
        'adv_is_jury'
    ];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

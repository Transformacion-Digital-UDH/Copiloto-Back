<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;



class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'thesis_title',
        'thesis_status',
        'document_url',
        'advisor_id',
    ];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el asesor (otro usuario)
    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }
}

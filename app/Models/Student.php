<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;



class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'stu_name',
        'stu_lastname_m',
        'stu_latsname_f',
        'stu_dni',
        'stu_code',
        'stu_user_id',
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

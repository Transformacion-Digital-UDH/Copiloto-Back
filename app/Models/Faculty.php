<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Facultie extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'faculties'; // Nombre de la colección en MongoDB
    protected $fillable = [

        'user_id',
        'fa_rank',
        'fa_name',
        'fa_lastname_m',
        'fa_lastname_f',
        'fa_faculty',

    ];
}

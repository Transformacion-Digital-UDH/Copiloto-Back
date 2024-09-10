<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Adviser extends Model
{
    use HasFactory;

    protected $connecion = 'mongodb';

    protected $fillable = [
        'name',
        'lastname_f', //apellido paterno
        'lastname_m', //apellido materno
        'orcid',
        'jury_id',
    ];
}

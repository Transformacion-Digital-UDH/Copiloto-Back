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
        'lastname_m',
        'latsname_f',
        'orcid',
        'jury_id',
    ];
}

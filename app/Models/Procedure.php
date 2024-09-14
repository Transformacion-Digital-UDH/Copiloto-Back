<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Procedure extends Model
{
    use HasFactory;

    protected $connecion = 'mongodb';

    protected $fillable = [
        'name',
        'expediente',
        'student_id',
        'state_id',
        'secre_school_id',
        'secre_pa_id',
    ];
}

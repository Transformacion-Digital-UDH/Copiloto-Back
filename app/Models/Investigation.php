<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Investigation extends Model
{
    use HasFactory;
    protected $connecion = 'mongodb';

    protected $fillable = [
        'type_id',
        'archive',
        'student_id',
        'jury_id',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $connecion = 'mongodb';

    protected $fillable = [
        'student_id',
        'name',
        'tipe',
        'archive',
        'state_id',
    ];
}

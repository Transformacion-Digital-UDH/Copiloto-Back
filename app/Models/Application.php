<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Application extends Model
{
    use HasFactory;
 
    protected $connection = 'mongodb';

    protected $fillable = [
        'student_id',
        'adviser_id',
        'state_id',
    ];
}

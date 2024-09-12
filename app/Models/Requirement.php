<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Requirement extends Model
{
    use HasFactory;

    protected $connecion = 'mongodb';

    protected $fillable = [
        'name',
        'procedure_id',
    ];
}

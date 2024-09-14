<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Correction extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    protected $fillable = [
        'investigation_id',
        'adviser_id',
        'state_id',
        'archive',
        'comment',
    ];
}

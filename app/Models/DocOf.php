<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class DocOf extends Model
{
    use HasFactory;
    
    protected $connection = 'mongodb';
    protected $collection = 'docofs'; // Nombre de la colección en MongoDB


    protected $fillable = [
        "student_id",
        "docof_num_of",
        "docof_status",
        "docof_date_emit"
    ];
}

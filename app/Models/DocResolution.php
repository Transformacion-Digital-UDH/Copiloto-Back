<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class DocResolution extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'docresolutions'; // Nombre de la colección en MongoDB


    protected $fillable = [
        "docres_student_id",
        "docres_status",
        "docres_num_res",
        "docres_date_emit"
    ];
}

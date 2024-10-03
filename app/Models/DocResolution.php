<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\HasOne;

class DocResolution extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'docresolutions'; // Nombre de la colección en MongoDB


    protected $fillable = [
        "docof_id",
        "docres_name",
        "student_id",
        'docres_observation',
        "docres_status",
        "docres_num_res",
        "docres_date_emit",
        "docres_observation"
    ];

    public function docof(): HasOne
    {
        return $this->hasOne(DocOf::class, '_id', 'docof_id');
    }

    public function getCreatedFormattedAttribute(){ 
        return $this->created_at->format('d-m-Y');
    }
}

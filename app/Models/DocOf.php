<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;
use MongoDB\Laravel\Relations\HasOne;

class DocOf extends Model
{
    use HasFactory;
    
    protected $connection = 'mongodb';
    protected $collection = 'docofs'; // Nombre de la colección en MongoDB


    protected $fillable = [
        "of_name",
        "solicitude_id",
        "student_id",
        "of_num_of",
        "of_num_exp",
        "of_status", //pendiente , observado, tramitado
        "of_observation",
        "of_fecha",
        "of_hora",
    ];
    
    public function solicitude(): HasOne
    {
        return $this->hasOne(Solicitude::class, '_id', 'solicitude_id');
    }

    public function docresolution(): HasOne
    {
        return $this->hasOne(DocResolution::class);
    }

    public function getCreatedFormattedAttribute(){ 
        return $this->created_at->format('d-m-Y');
    }
    
}

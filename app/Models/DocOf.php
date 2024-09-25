<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\HasOne;

class DocOf extends Model
{
    use HasFactory;
    
    protected $connection = 'mongodb';
    protected $collection = 'docofs'; // Nombre de la colección en MongoDB


    protected $fillable = [
        "of_name",
        "solicitude_id",
        "of_num_of",
        "of_status",
    ];
    
    public function solicitude(): HasOne
    {
        return $this->hasOne(Solicitude::class, '_id', 'solicitude_id');
    }
    
}

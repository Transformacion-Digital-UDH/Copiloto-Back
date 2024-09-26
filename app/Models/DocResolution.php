<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;

class DocResolution extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'docresolutions'; // Nombre de la colección en MongoDB


    protected $fillable = [
        "docof_id",
        "docres_name",
        "docres_status",
        "docres_num_res",
    ];

    public function docof(): BelongsTo
    {
        return $this->belongsTo(DocOf::class);
    }
    
    public function getCreatedFormattedAttribute(){ 
        return $this->created_at->format('d-m-Y');
    }
}

<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class History extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'history';

    protected $fillable = [
        'solicitude_id',
        'action',
        'sol_title_inve',
        'observation',
        'adviser_id'
    ];
    
    public function getCreatedFormattedAttribute(){ 
        return $this->created_at->format('d-m-Y');
    }

    public function adviser()
    {
        return $this->belongsTo(Adviser::class, 'adviser_id');
    }
}

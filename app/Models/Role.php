<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsToMany;

class Role extends Model
{
    protected $collection = 'roles';
    protected $fillable = ['name', 'permission_ids']; // 'permission_ids' es un array de IDs de permisos
    
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, null, 'permission_ids', '_id');
    }
}


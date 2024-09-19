<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;
use App\Models\Permission;
use MongoDB\Laravel\Relations\HasOne;

class User extends Authenticatable
{
    protected $connection = 'mongodb';

    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relación con el rol
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // Verificar si el usuario tiene un permiso específico basado en IDs de permisos
    public function hasPermission($permissionName)
    {
        $role = $this->role()->first();

        if (!$role) {
            \Log::warning('User has no role assigned', ['user_id' => $this->id]);
            return false;
        }

        // Obtener los IDs de los permisos del rol
        $permissionIds = $role->permission_ids ?? [];

        // Buscar el permiso por nombre
        $permission = Permission::where('name', $permissionName)->first();

        if (!$permission) {
            \Log::warning('Permission not found', ['permission_name' => $permissionName]);
            return false;
        }

        // Verificar si el ID del permiso está en el array de IDs del rol
        return in_array($permission->_id, $permissionIds);
    }

    // Relaciones adicionales

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }
}

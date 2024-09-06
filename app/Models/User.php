<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{

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
        'role',
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

    // Métodos para verificar roles
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    public function isAdvisor()
    {
        return $this->role === 'advisor';
    }

    public function isJurado()
    {
        return $this->role === 'jurado';
    }

    public function ispaisi()
    {
        return $this->role === 'paisi';
    }

    public function isfacultad()
    {
        return $this->role === 'facultad';
    }

    public function iscoordinador()
    {
        return $this->role === 'coordinador';
    }

    // Relaciones
    
    public function student()
    {
        return $this->hasOne(Student::class);
    }


}

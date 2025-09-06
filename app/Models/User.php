<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Notification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usuarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'status',
        'approved_at',
        'approved_by',
        'rejection_reason', // campo agregado para que verifique la razon 
        'blocked_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    /**
     * Relación con notificaciones
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
    
    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin()
    {
        return $this->role === 'administrador';
    }
    
    /**
     * Verificar si el usuario es fiscalizador
     */
    public function isFiscalizador()
    {
        return $this->role === 'fiscalizador';
    }
    
    /**
     * Verificar si el usuario es de ventanilla
     */
    public function isVentanilla()
    {
        return $this->role === 'ventanilla';
    }
    
    /**
     * Verificar si el usuario está aprobado
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }
    
    /**
     * Verificar si el usuario está pendiente de aprobación
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
    
    /**
     * Verificar si el usuario fue rechazado
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }
    
    /**
     * Relación con el usuario que aprobó
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    /**
     * Verificar si el usuario tiene uno o varios roles
     */
    public function hasRole($roles)
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return $this->role === $roles;
    }
}

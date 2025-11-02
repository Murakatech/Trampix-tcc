<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'display_name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relações - Agora permite múltiplos perfis
    public function companies()
    {
        return $this->hasMany(\App\Models\Company::class);
    }

    public function freelancers()
    {
        return $this->hasMany(\App\Models\Freelancer::class);
    }

    // Relações para perfil ativo (compatibilidade)
    public function company()
    {
        return $this->hasOne(\App\Models\Company::class)->where('is_active', true);
    }

    public function freelancer()
    {
        return $this->hasOne(\App\Models\Freelancer::class)->where('is_active', true);
    }

    // Métodos helper para verificar tipos de perfil
    public function isFreelancer()
    {
        return $this->freelancers()->where('is_active', true)->exists();
    }

    public function isCompany()
    {
        return $this->companies()->where('is_active', true)->exists();
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function hasActiveProfile($type)
    {
        if ($type === 'freelancer') {
            return $this->isFreelancer();
        }
        if ($type === 'company') {
            return $this->isCompany();
        }
        return false;
    }

    // Método para criar perfil
    public function createProfile($type, $data = [])
    {
        if ($type === 'freelancer') {
            return $this->freelancers()->create(array_merge(['is_active' => true], $data));
        }
        if ($type === 'company') {
            return $this->companies()->create(array_merge(['is_active' => true], $data));
        }
        return null;
    }

    // Método para verificar se o usuário tem múltiplos perfis
    public function hasMultipleRoles()
    {
        $rolesCount = 0;
        
        if ($this->isFreelancer()) {
            $rolesCount++;
        }
        
        if ($this->isCompany()) {
            $rolesCount++;
        }
        
        if ($this->isAdmin()) {
            $rolesCount++;
        }
        
        return $rolesCount > 1;
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Accessor para obter o caminho da foto de perfil do perfil ativo
     */
    public function getProfilePhotoPathAttribute()
    {
        if ($this->isFreelancer() && $this->freelancer && $this->freelancer->profile_photo) {
            return $this->freelancer->profile_photo;
        }
        
        if ($this->isCompany() && $this->company && $this->company->profile_photo) {
            return $this->company->profile_photo;
        }
        
        return null;
    }
}

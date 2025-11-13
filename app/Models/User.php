<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        // Prioriza o perfil ativo em sessão para alternar corretamente
        $activeRole = session('active_role');

        // Quando há um papel ativo definido, NÃO fazer fallback para outro perfil.
        if ($activeRole === 'freelancer') {
            return ($this->freelancer && $this->freelancer->profile_photo)
                ? $this->freelancer->profile_photo
                : null;
        }

        if ($activeRole === 'company') {
            return ($this->company && $this->company->profile_photo)
                ? $this->company->profile_photo
                : null;
        }

        // Sem papel ativo definido: fallback ordenado freelancer → company
        if (! $activeRole) {
            if ($this->freelancer && $this->freelancer->profile_photo) {
                return $this->freelancer->profile_photo;
            }

            if ($this->company && $this->company->profile_photo) {
                return $this->company->profile_photo;
            }
        }

        return null;
    }

    /**
     * URL pública para a foto de perfil do perfil ativo
     */
    public function getProfilePhotoUrlAttribute()
    {
        $path = $this->profile_photo_path;
        if (! $path) {
            return null;
        }

        // Sempre servir do disco "public"
        return asset('storage/'.ltrim($path, '/'));
    }

    /**
     * Nome de exibição dinâmico baseado no perfil ativo
     */
    public function getDisplayNameAttribute($value)
    {
        // Se houver um nome salvo diretamente no usuário, mantém como fallback
        $fallback = $value ?: $this->attributes['name'] ?? null;

        $activeRole = session('active_role');
        if ($activeRole === 'freelancer' && $this->freelancer) {
            return $this->freelancer->display_name ?: $fallback;
        }

        if ($activeRole === 'company' && $this->company) {
            return $this->company->display_name ?: $fallback;
        }

        return $fallback;
    }

    /**
     * Iniciais para placeholder do avatar, derivadas do nome de exibição
     */
    public function getInitialsAttribute()
    {
        $name = $this->display_name ?: ($this->attributes['name'] ?? '');
        $name = trim($name);
        if ($name === '') {
            // Fallback para parte antes do @ do email
            $email = $this->attributes['email'] ?? '';
            $base = $email ? explode('@', $email)[0] : '';
            $name = $base ?: 'U';
        }

        // Pega as primeiras letras de até duas palavras
        $parts = preg_split('/\s+/', $name);
        $initials = '';
        foreach ($parts as $part) {
            if ($part !== '') {
                $initials .= mb_strtoupper(mb_substr($part, 0, 1));
            }
            if (mb_strlen($initials) >= 2) {
                break;
            }
        }

        return $initials ?: 'U';
    }
}

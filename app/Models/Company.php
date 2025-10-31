<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'cnpj',
        'sector',
        'location',
        'description',
        'website',
        'phone',
        'employees_count',
        'founded_year',
        'is_active',
        'profile_photo',
    ];

    protected $casts = [
        'employees_count' => 'integer',
        'founded_year' => 'integer',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vacancies()
    {
        return $this->hasMany(JobVacancy::class);
    }

    public function jobVacancies()
    {
        return $this->hasMany(JobVacancy::class, 'company_id');
    }
}

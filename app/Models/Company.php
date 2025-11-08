<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'name',
        'cnpj',
        'sector',
        'location',
        'description',
        'website',
        'email',
        'phone',
        'company_size',
        'employees_count',
        'founded_year',
        'is_active',
        'profile_photo',
        'activity_area_id',
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

    public function serviceCategories()
    {
        return $this->belongsToMany(ServiceCategory::class);
    }

    public function sectors()
    {
        return $this->belongsToMany(\App\Models\Sector::class, 'company_sector');
    }

    public function activityArea()
    {
        return $this->belongsTo(\App\Models\ActivityArea::class);
    }
}

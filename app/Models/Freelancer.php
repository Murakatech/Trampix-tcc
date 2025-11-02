<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Freelancer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'bio',
        'portfolio_url',
        'cv_url',
        'phone',
        'location',
        'hourly_rate',
        'availability',
        'is_active',
        'profile_photo',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function serviceCategories()
    {
        return $this->belongsToMany(ServiceCategory::class);
    }
}

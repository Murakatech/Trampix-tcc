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
        'whatsapp',
        'location',
        'hourly_rate',
        'availability',
        'is_active',
        'profile_photo',
        // Deprecated: activity_area_id removed from UI; kept for legacy compatibility
        'activity_area_id',
        // New primary segment association
        'segment_id',
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

    public function sectors()
    {
        return $this->belongsToMany(\App\Models\Sector::class, 'freelancer_sector');
    }

    public function segments()
    {
        return $this->belongsToMany(\App\Models\Segment::class, 'freelancer_segment');
    }

    public function activityArea()
    {
        return $this->belongsTo(\App\Models\ActivityArea::class);
    }

    /**
     * Primary Segment the freelancer belongs to
     */
    public function segment()
    {
        return $this->belongsTo(\App\Models\Segment::class);
    }
}

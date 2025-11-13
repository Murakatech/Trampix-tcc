<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Preference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'role', 'desired_roles', 'segments', 'skills', 'seniority_min', 'seniority_max', 'remote_ok', 'salary_min', 'salary_max', 'location', 'radius_km',
    ];

    protected $casts = [
        'desired_roles' => 'array',
        'segments' => 'array',
        'skills' => 'array',
        'remote_ok' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

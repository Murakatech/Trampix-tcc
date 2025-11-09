<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function freelancers()
    {
        return $this->belongsToMany(Freelancer::class, 'freelancer_segment');
    }
}
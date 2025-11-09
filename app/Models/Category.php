<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'segment_id',
    ];

    public function segment()
    {
        return $this->belongsTo(Segment::class);
    }

    public function jobVacancies()
    {
        return $this->hasMany(JobVacancy::class);
    }
}
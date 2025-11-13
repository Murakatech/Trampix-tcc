<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
    ];

    public function freelancers()
    {
        return $this->hasMany(Freelancer::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}

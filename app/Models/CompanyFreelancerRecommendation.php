<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyFreelancerRecommendation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'company_id', 'freelancer_id', 'score', 'batch_date', 'status', 'decided_at', 'created_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'batch_date' => 'date',
        'decided_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class);
    }
}


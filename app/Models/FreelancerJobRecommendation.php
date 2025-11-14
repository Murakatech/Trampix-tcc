<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreelancerJobRecommendation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'freelancer_id', 'job_vacancy_id', 'score', 'batch_date', 'status', 'decided_at', 'created_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'batch_date' => 'date',
        'decided_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class);
    }

    public function job()
    {
        return $this->belongsTo(JobVacancy::class, 'job_vacancy_id');
    }
}


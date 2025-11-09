<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'job_vacancy_id',
        'freelancer_id',
        'cover_letter',
        'status',
        // Avaliações
        'company_rating',
        'company_comment',
        'company_ratings_json',
        'company_rating_avg',
        'evaluated_by_company_at',
        'freelancer_rating',
        'freelancer_comment',
        'freelancer_ratings_json',
        'freelancer_rating_avg',
        'evaluated_by_freelancer_at',
        'rejected_acknowledged',
    ];

    protected $casts = [
        'evaluated_by_company_at' => 'datetime',
        'evaluated_by_freelancer_at' => 'datetime',
        'rejected_acknowledged' => 'boolean',
        'company_ratings_json' => 'array',
        'freelancer_ratings_json' => 'array',
        'company_rating_avg' => 'decimal:1',
        'freelancer_rating_avg' => 'decimal:1',
    ];

    public function jobVacancy()
    {
        return $this->belongsTo(JobVacancy::class);
    }

    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class);
    }
}

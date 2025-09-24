<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_vacancy_id',
        'freelancer_id',
        'cover_letter',
        'status',
    ];

    public function vacancy()
    {
        return $this->belongsTo(JobVacancy::class, 'job_vacancy_id');
    }

    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class);
    }
}

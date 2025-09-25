<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = ['job_vacancy_id','freelancer_id','cover_letter','status'];

    public function jobVacancy()
    {
        return $this->belongsTo(JobVacancy::class);
    }

    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class);
    }
}

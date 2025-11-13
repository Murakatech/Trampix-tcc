<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BaseMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';
    public $timestamps = false; // apenas created_at

    protected $fillable = [
        'freelancer_id','job_vacancy_id','created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function freelancer(): BelongsTo { return $this->belongsTo(Freelancer::class); }
    public function jobVacancy(): BelongsTo { return $this->belongsTo(JobVacancy::class); }
}
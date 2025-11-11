<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Recommendation extends Model
{
    use HasFactory;

    public $timestamps = false; // usamos created_at manualmente

    protected $fillable = [
        'subject_type','subject_id','target_type','target_id','score','batch_date','status','decided_at','created_at'
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'batch_date' => 'date',
        'decided_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Relações auxiliares conforme tipo
    public function subjectFreelancer(): BelongsTo { return $this->belongsTo(Freelancer::class, 'subject_id'); }
    public function subjectCompany(): BelongsTo { return $this->belongsTo(Company::class, 'subject_id'); }
    public function targetJob(): BelongsTo { return $this->belongsTo(JobVacancy::class, 'target_id'); }
    public function targetFreelancer(): BelongsTo { return $this->belongsTo(Freelancer::class, 'target_id'); }

    /**
     * Helper estático: filtra recomendações para o usuário informado.
     */
    public static function forUser(User $user): Builder
    {
        $subjectType = null;
        $subjectId = null;

        if ($user->isFreelancer() && $user->freelancer) {
            $subjectType = 'freelancer';
            $subjectId = $user->freelancer->id;
        } elseif ($user->isCompany() && $user->company) {
            $subjectType = 'company';
            $subjectId = $user->company->id;
        }

        return static::query()
            ->when($subjectType && $subjectId, function (Builder $q) use ($subjectType, $subjectId) {
                $q->where('subject_type', $subjectType)
                  ->where('subject_id', $subjectId);
            });
    }
}
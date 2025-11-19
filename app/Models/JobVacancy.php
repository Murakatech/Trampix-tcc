<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobVacancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'requirements',
        'category_id',
        'service_category_id',
        'location_type',
        'salary_range',
        'salary_min',
        'salary_max',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    /**
     * Scopes utilitários para simplificar filtros no controlador
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopePublicList(Builder $query): Builder
    {
        return $query->active()
            ->whereDoesntHave('applications', function ($q) {
                $q->whereIn('status', ['accepted']);
            });
    }

    public function scopeNotAppliedBy(Builder $query, ?int $freelancerId): Builder
    {
        if ($freelancerId) {
            $query->whereDoesntHave('applications', function ($q) use ($freelancerId) {
                $q->where('freelancer_id', $freelancerId);
            });
        }

        return $query;
    }

    public function scopeFilterCategories(Builder $query, array $categories): Builder
    {
        if (empty($categories)) {
            return $query;
        }
        $categoryIds = \App\Models\Category::whereIn('name', $categories)->pluck('id');

        if ($categoryIds->count() === 0) {
            return $query;
        }
        return $query->whereIn('category_id', $categoryIds);
    }

    public function scopeFilterSegment(Builder $query, int $segmentId): Builder
    {
        if (! $segmentId) {
            return $query;
        }
        $segmentCategoryQuery = \App\Models\Category::where('segment_id', $segmentId)->select('id', 'name');
        $segmentCategoryIds = $segmentCategoryQuery->pluck('id');
        $segmentCategoryNames = $segmentCategoryQuery->pluck('name');

        if ($segmentCategoryIds->count() === 0) {
            return $query;
        }
        return $query->whereIn('category_id', $segmentCategoryIds);
    }

    // contract_type removido do sistema: todos os contratos são freelance

    public function scopeLocationType(Builder $query, ?string $type): Builder
    {
        return $type ? $query->where('location_type', $type) : $query;
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('requirements', 'like', "%{$search}%")
                ->orWhereHas('company', function ($companyQuery) use ($search) {
                    $companyQuery->where('name', 'like', "%{$search}%");
                });
        });
    }
}

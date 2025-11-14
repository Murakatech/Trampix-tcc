<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Freelancer;
use App\Models\JobVacancy;
use App\Models\Preference;
use App\Models\Recommendation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecommendationService
{
    /**
     * Função pura e explicável de score 0..1 baseada em SEGMENTOS
     * (depois ordena naturalmente: primeiro segmentos semelhantes, depois outros).
     */
    public function computeScore(array $segmentsUser, array $segmentsTarget, ?array $faixaU, ?array $faixaT, ?array $modalU, ?array $modalT, float $confidence): float
    {
        $segJaccard = $this->jaccard($segmentsUser, $segmentsTarget);
        $faixa = $this->faixaCompat($faixaU, $faixaT);
        $modal = $this->modalidadeFit($modalU, $modalT);
        $conf = max(0.0, min(1.0, $confidence));

        // Pesos priorizando segmentos
        $score = 0.60 * $segJaccard
               + 0.20 * $modal
               + 0.15 * $faixa
               + 0.05 * $conf;

        return round(max(0.0, min(1.0, $score)), 4);
    }

    private function jaccard(array $a, array $b): float
    {
        $setA = collect($a)->filter()->map(fn ($s) => Str::lower(trim($s)))->unique();
        $setB = collect($b)->filter()->map(fn ($s) => Str::lower(trim($s)))->unique();
        if ($setA->isEmpty() && $setB->isEmpty()) {
            return 0.0;
        }
        $inter = $setA->intersect($setB)->count();
        $union = $setA->merge($setB)->unique()->count();

        return $union > 0 ? $inter / $union : 0.0;
    }

    private function seniorityFit(?int $u, ?int $t): float
    {
        if (! $u && ! $t) {
            return 0.5;
        }
        if ($u && $t) {
            $diff = abs($u - $t);

            return max(0.0, 1.0 - ($diff * 0.5));
        }

        return 0.5;
    }

    private function faixaCompat(?array $u, ?array $t): float
    {
        if (! $u || ! $t) {
            return 0.5;
        }
        [$umin, $umax] = [$u['min'] ?? null, $u['max'] ?? null];
        [$tmin, $tmax] = [$t['min'] ?? null, $t['max'] ?? null];
        if ($umin === null || $umax === null || $tmin === null || $tmax === null) {
            return 0.5;
        }
        if ($umax < $tmin || $tmax < $umin) {
            return 0.0;
        }
        if (($tmin >= $umin && $tmin <= $umax) || ($umin >= $tmin && $umin <= $tmax)) {
            return 1.0;
        }

        return 0.7;
    }

    private function modalidadeFit(?array $u, ?array $t): float
    {
        if (! $u || ! $t) {
            return 0.5;
        }
        if (! empty($u['remote_ok'])) {
            return 1.0;
        }
        if (! empty($t['location_type']) && Str::lower($t['location_type']) === 'remoto') {
            return 1.0;
        }
        $radius = (int) ($u['radius_km'] ?? 0);
        $locU = trim((string) ($u['location'] ?? ''));
        $locT = trim((string) ($t['location'] ?? ''));
        if ($radius <= 0 || $locU === '' || $locT === '') {
            return 0.5;
        }
        if (Str::lower($locU) === Str::lower($locT)) {
            return 1.0;
        }

        return 0.5;
    }

    private function sinalConfiancaForTarget($target): float
    {
        if ($target instanceof JobVacancy) {
            $company = $target->company;
            $signals = 0;
            $total = 5;
            if ($company) {
                $signals += $company->website ? 1 : 0;
                $signals += $company->linkedin_url ? 1 : 0;
                $signals += $company->description ? 1 : 0;
                // setor: considerar relacionamento normalizado
                try {
                    $hasSector = $company->sectors()->exists();
                } catch (\Throwable $e) { $hasSector = false; }
                $signals += $hasSector ? 1 : 0;
                $signals += $company->is_active ? 1 : 0;
            }

            return $total > 0 ? $signals / $total : 0.5;
        }
        if ($target instanceof Freelancer) {
            $signals = 0;
            $total = 5;
            $signals += $target->portfolio_url ? 1 : 0;
            $signals += $target->linkedin_url ? 1 : 0;
            $signals += $target->cv_url ? 1 : 0;
            $signals += $target->bio ? 1 : 0;
            $signals += $target->is_active ? 1 : 0;

            return $total > 0 ? $signals / $total : 0.5;
        }

        return 0.5;
    }

    /**
     * Gera recomendações diárias para o usuário informado. Retorna quantidade gerada.
     */
    public function generateDailyBatchFor(User $user, int $topN = 50, bool $segmentOnly = false): int
    {
        $today = Carbon::today();
        $pref = Preference::where('user_id', $user->id)->first();
        // Base: segmentos do usuário (preferências), com fallback robusto para TODOS segmentos do perfil
        $segmentsUser = collect($pref?->segments ?? [])
            ->filter()->values()->all();
        if (empty($segmentsUser)) {
            if ($user->isFreelancer() && $user->freelancer) {
                // Usar todos os segmentos do freelancer (primário + muitos)
                $segmentsUser = $this->segmentsForFreelancer($user->freelancer);
            } elseif ($user->isCompany() && $user->company) {
                // Usar segmentos da empresa (primário + muitos)
                $segmentsUser = $this->segmentsForCompany($user->company);
            }
        }
        // Preferências de faixa e modalidade com defaults do perfil, quando disponíveis
        $faixaU = ['min' => $pref?->salary_min, 'max' => $pref?->salary_max];
        $modalU = [
            'remote_ok' => (bool) ($pref?->remote_ok ?? false),
            'radius_km' => $pref?->radius_km,
            'location' => $pref?->location ?? ($user->isFreelancer() ? ($user->freelancer?->location) : ($user->company?->location)),
        ];

        if ($user->isFreelancer() && $user->freelancer) {
            $subjectId = $user->freelancer->id;
            $query = JobVacancy::query()->active()->notAppliedBy($subjectId);
            if (! empty($segmentsUser)) {
                $query->where(function ($q) use ($segmentsUser) {
                    $q->whereHas('category', function ($cq) use ($segmentsUser) {
                        $cq->whereIn('segment_id', $segmentsUser);
                    })->orWhereHas('company', function ($compQ) use ($segmentsUser) {
                        $compQ->whereIn('segment_id', $segmentsUser);
                    });
                });
            }
            $candidates = $query->inRandomOrder()->limit($topN)->get();
            foreach ($candidates as $job) {
                \App\Models\FreelancerJobRecommendation::updateOrCreate(
                    ['freelancer_id' => $subjectId, 'job_vacancy_id' => $job->id],
                    [
                        'score' => 0.0,
                        'batch_date' => $today,
                        'status' => 'pending',
                        'created_at' => Carbon::now(),
                    ]
                );
            }

            return $candidates->count();
        }

        if ($user->isCompany() && $user->company) {
            $subjectId = $user->company->id;
            $query = Freelancer::query()->where('is_active', true);
            if (! empty($segmentsUser)) {
                $query->whereHas('segments', function ($q) use ($segmentsUser) {
                    $q->whereIn('segments.id', $segmentsUser);
                });
            }
            $candidates = $query->inRandomOrder()->limit($topN)->get();
            foreach ($candidates as $freelancer) {
                \App\Models\CompanyFreelancerRecommendation::updateOrCreate(
                    ['company_id' => $subjectId, 'freelancer_id' => $freelancer->id],
                    [
                        'score' => 0.0,
                        'batch_date' => $today,
                        'status' => 'pending',
                        'created_at' => Carbon::now(),
                    ]
                );
            }

            return $candidates->count();
        }

        return 0;
    }

    /**
     * Prepara recomendações para a empresa com contexto de uma vaga específica:
     * limpa pendentes e gera recomendações de freelancers alinhados ao segmento da vaga.
     */
    public function prepareCompanyConnectForJob(User $user, int $jobId, int $topN = 50, bool $segmentOnly = false): int
    {
        if (! ($user->isCompany() && $user->company)) {
            return 0;
        }
        $company = $user->company;
        $job = JobVacancy::query()->where('id', $jobId)->where('company_id', $company->id)->first();
        if (! $job) {
            return 0;
        }

        $today = Carbon::today();
        // Substituir segmentos do usuário pelos segmentos inferidos da VAGA selecionada
        $segmentsUser = $this->segmentsForJob($job);

        // Limpar recomendações pendentes para evitar mistura de contexto
        \App\Models\CompanyFreelancerRecommendation::query()
            ->where('company_id', $company->id)
            ->where('status', 'pending')
            ->delete();

        $subjectId = $company->id;
        $query = Freelancer::query()->where('is_active', true);
        if (! empty($segmentsUser)) {
            $query->whereHas('segments', function ($q) use ($segmentsUser) {
                $q->whereIn('segments.id', $segmentsUser);
            });
        }
        $top = $query->inRandomOrder()->limit($topN)->get();
        foreach ($top as $freelancer) {
            \App\Models\CompanyFreelancerRecommendation::updateOrCreate(
                ['company_id' => $subjectId, 'freelancer_id' => $freelancer->id],
                [
                    'score' => 0.0,
                    'batch_date' => $today,
                    'status' => 'pending',
                    'created_at' => Carbon::now(),
                ]
            );
        }

        return $top->count();
    }

    /**
     * Próximo card: recommendation pending ordenada por score.
     */
    public function nextCardFor(User $user): ?Recommendation
    {
        if ($user->isFreelancer() && $user->freelancer) {
            return \App\Models\FreelancerJobRecommendation::query()
                ->where('freelancer_id', $user->freelancer->id)
                ->where('status', 'pending')
                ->inRandomOrder()
                ->first();
        }
        if ($user->isCompany() && $user->company) {
            return \App\Models\CompanyFreelancerRecommendation::query()
                ->where('company_id', $user->company->id)
                ->where('status', 'pending')
                ->inRandomOrder()
                ->first();
        }
        return null;
    }

    /**
     * Processa decisão e cria Match quando ambos deram like. Suporta undo.
     */
    public function decide(User $user, int $recommendationId, string $action, ?int $jobId = null): array
    {
        $rec = null;
        $type = null;
        if ($user->isFreelancer() && $user->freelancer) {
            $rec = \App\Models\FreelancerJobRecommendation::query()
                ->where('id', $recommendationId)
                ->where('freelancer_id', $user->freelancer->id)
                ->first();
            $type = 'fjr';
        } elseif ($user->isCompany() && $user->company) {
            $rec = \App\Models\CompanyFreelancerRecommendation::query()
                ->where('id', $recommendationId)
                ->where('company_id', $user->company->id)
                ->first();
            $type = 'cfr';
        }
        if (! $rec) {
            return ['ok' => false, 'match' => false, 'error' => 'recommendation_not_found'];
        }
        $now = Carbon::now();
        if ($action === 'undo') {
            if ($rec->status === 'rejected' && $rec->decided_at && $now->diffInSeconds($rec->decided_at) <= 5) {
                $rec->status = 'pending';
                $rec->decided_at = null;
                $rec->save();

                return ['ok' => true, 'undone' => true, 'match' => false];
            }

            return ['ok' => false, 'match' => false, 'error' => 'undo_not_allowed'];
        }
        if (! in_array($action, ['liked', 'rejected', 'saved'])) {
            return ['ok' => false, 'match' => false, 'error' => 'invalid_action'];
        }
        $rec->status = $action;
        $rec->decided_at = $now;
        $rec->save();
        $hasMatch = false;
        if ($action === 'liked') {
            if ($type === 'fjr') {
                $freelancerId = $rec->freelancer_id;
                $jobId = $rec->job_vacancy_id;
                $job = JobVacancy::find($jobId);
                if ($job && $job->company) {
                    $companyId = $job->company->id;
                    $inverseLiked = \App\Models\CompanyFreelancerRecommendation::query()
                        ->where('company_id', $companyId)
                        ->where('freelancer_id', $freelancerId)
                        ->where('status', 'liked')
                        ->exists();
                    if ($inverseLiked) {
                        $exists = DB::table('matches')
                            ->where('freelancer_id', $freelancerId)
                            ->where('job_vacancy_id', $jobId)
                            ->exists();
                        if (! $exists) {
                            DB::table('matches')->insert([
                                'freelancer_id' => $freelancerId,
                                'job_vacancy_id' => $jobId,
                                'created_at' => Carbon::now(),
                            ]);
                        }
                        $hasMatch = true;
                    }
                }
            } elseif ($type === 'cfr') {
                $companyId = $rec->company_id;
                $freelancerId = $rec->freelancer_id;
                $resolvedJobId = $jobId ?: JobVacancy::query()->where('company_id', $companyId)->value('id');
                if ($resolvedJobId) {
                    $inverseLiked = \App\Models\FreelancerJobRecommendation::query()
                        ->where('freelancer_id', $freelancerId)
                        ->where('job_vacancy_id', $resolvedJobId)
                        ->where('status', 'liked')
                        ->exists();
                    if ($inverseLiked) {
                        $exists = DB::table('matches')
                            ->where('freelancer_id', $freelancerId)
                            ->where('job_vacancy_id', $resolvedJobId)
                            ->exists();
                        if (! $exists) {
                            DB::table('matches')->insert([
                                'freelancer_id' => $freelancerId,
                                'job_vacancy_id' => $resolvedJobId,
                                'created_at' => Carbon::now(),
                            ]);
                        }
                        $hasMatch = true;
                    }
                }
            }
        }

        return ['ok' => true, 'match' => $hasMatch];
    }

    /**
     * Determina segmentos para uma vaga a partir da categoria ou da empresa.
     */
    private function segmentsForJob(JobVacancy $job): array
    {
        $segments = [];
        if ($job->category_id) {
            $cat = \App\Models\Category::find($job->category_id);
            if ($cat?->segment_id) {
                $segments[] = (int) $cat->segment_id;
            }
        }
        // fallback: usar segmento da empresa
        if (empty($segments) && $job->company?->segment_id) {
            $segments[] = (int) $job->company->segment_id;
        }

        return array_values(array_unique($segments));
    }

    /**
     * Determina segmentos de um freelancer (id principal + múltiplos relacionamentos), se existentes.
     */
    private function segmentsForFreelancer(Freelancer $freelancer): array
    {
        $segs = [];
        if ($freelancer->segment_id) {
            $segs[] = (int) $freelancer->segment_id;
        }
        try {
            $many = $freelancer->segments()->pluck('segments.id')->all();
            foreach ($many as $sid) {
                $segs[] = (int) $sid;
            }
        } catch (\Throwable $e) {
        }

        return array_values(array_unique($segs));
    }

    /**
     * Determina segmentos de uma empresa (id principal + múltiplos relacionamentos), se existentes.
     */
    private function segmentsForCompany(Company $company): array
    {
        $segs = [];
        if ($company->segment_id) {
            $segs[] = (int) $company->segment_id;
        }
        try {
            $many = $company->segments()->pluck('segments.id')->all();
            foreach ($many as $sid) {
                $segs[] = (int) $sid;
            }
        } catch (\Throwable $e) {
        }

        return array_values(array_unique($segs));
    }

    private function parseSalaryRange(?string $text): ?array
    {
        if (! $text) {
            return null;
        }
        $digits = [];
        preg_match_all('/(\d+[\.,]?\d*)/u', $text, $m);
        foreach (($m[1] ?? []) as $raw) {
            $clean = (float) str_replace([',', '.'], '', $raw);
            if ($clean > 0) {
                $digits[] = $clean;
            }
        }
        if (count($digits) >= 2) {
            sort($digits);

            return ['min' => $digits[0], 'max' => $digits[1]];
        }
        if (count($digits) === 1) {
            return ['min' => $digits[0] * 0.9, 'max' => $digits[0] * 1.1];
        }

        return null;
    }
}

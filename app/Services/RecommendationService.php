<?php

namespace App\Services;

use App\Models\User;
use App\Models\Company;
use App\Models\Freelancer;
use App\Models\JobVacancy;
use App\Models\Preference;
use App\Models\Recommendation;
use App\Models\Match;
use App\Models\Skill;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RecommendationService
{
    /**
     * Função pura e explicável de score 0..1.
     */
    public function computeScore(array $skillsUser, array $skillsTarget, ?int $seniorityU, ?int $seniorityT, ?array $faixaU, ?array $faixaT, ?array $modalU, ?array $modalT, float $confidence): float
    {
        $jaccard = $this->jaccard($skillsUser, $skillsTarget);
        $senFit = $this->seniorityFit($seniorityU, $seniorityT);
        $faixa = $this->faixaCompat($faixaU, $faixaT);
        $modal = $this->modalidadeFit($modalU, $modalT);
        $conf = max(0.0, min(1.0, $confidence));

        $score = 0.50 * $jaccard
               + 0.15 * $senFit
               + 0.15 * $faixa
               + 0.10 * $modal
               + 0.10 * $conf;

        return round(max(0.0, min(1.0, $score)), 4);
    }

    private function jaccard(array $a, array $b): float
    {
        $setA = collect($a)->filter()->map(fn($s) => Str::lower(trim($s)))->unique();
        $setB = collect($b)->filter()->map(fn($s) => Str::lower(trim($s)))->unique();
        if ($setA->isEmpty() && $setB->isEmpty()) return 0.0;
        $inter = $setA->intersect($setB)->count();
        $union = $setA->merge($setB)->unique()->count();
        return $union > 0 ? $inter / $union : 0.0;
    }

    private function seniorityFit(?int $u, ?int $t): float
    {
        if (!$u && !$t) return 0.5;
        if ($u && $t) {
            $diff = abs($u - $t);
            return max(0.0, 1.0 - ($diff * 0.5));
        }
        return 0.5;
    }

    private function faixaCompat(?array $u, ?array $t): float
    {
        if (!$u || !$t) return 0.5;
        [$umin, $umax] = [$u['min'] ?? null, $u['max'] ?? null];
        [$tmin, $tmax] = [$t['min'] ?? null, $t['max'] ?? null];
        if ($umin === null || $umax === null || $tmin === null || $tmax === null) return 0.5;
        if ($umax < $tmin || $tmax < $umin) return 0.0;
        if (($tmin >= $umin && $tmin <= $umax) || ($umin >= $tmin && $umin <= $tmax)) return 1.0;
        return 0.7;
    }

    private function modalidadeFit(?array $u, ?array $t): float
    {
        if (!$u || !$t) return 0.5;
        if (!empty($u['remote_ok'])) return 1.0;
        if (!empty($t['location_type']) && Str::lower($t['location_type']) === 'remoto') return 1.0;
        $radius = (int)($u['radius_km'] ?? 0);
        $locU = trim((string)($u['location'] ?? ''));
        $locT = trim((string)($t['location'] ?? ''));
        if ($radius <= 0 || $locU === '' || $locT === '') return 0.5;
        if (Str::lower($locU) === Str::lower($locT)) return 1.0;
        return 0.5;
    }

    private function sinalConfiancaForTarget($target): float
    {
        if ($target instanceof JobVacancy) {
            $company = $target->company;
            $signals = 0; $total = 5;
            if ($company) {
                $signals += $company->website ? 1 : 0;
                $signals += $company->linkedin_url ? 1 : 0;
                $signals += $company->description ? 1 : 0;
                $signals += $company->sector ? 1 : 0;
                $signals += $company->is_active ? 1 : 0;
            }
            return $total > 0 ? $signals / $total : 0.5;
        }
        if ($target instanceof Freelancer) {
            $signals = 0; $total = 5;
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
    public function generateDailyBatchFor(User $user, int $topN = 50): int
    {
        $today = Carbon::today();
        $pref = Preference::where('user_id', $user->id)->first();
        if (!$pref) return 0;
        $skillsUser = (array)($pref->skills ?? []);
        $faixaU = ['min' => $pref->salary_min, 'max' => $pref->salary_max];
        $modalU = ['remote_ok' => $pref->remote_ok, 'radius_km' => $pref->radius_km, 'location' => $pref->location];
        $seniorityU = null;

        if ($user->isFreelancer() && $user->freelancer) {
            $subjectType = 'freelancer';
            $subjectId = $user->freelancer->id;
            $query = JobVacancy::query()->active()->notAppliedBy($subjectId);
            $desired = collect($pref->desired_roles ?? [])->filter()->values()->all();
            if (!empty($desired)) { $query->filterCategories($desired); }
            $segmentIds = collect($pref->segments ?? [])->filter()->values()->all();
            if (!empty($segmentIds)) { foreach ($segmentIds as $segId) { $query->filterSegment((int)$segId); } }
            $candidates = $query->limit(500)->get();
            $scored = [];
            foreach ($candidates as $job) {
                $skillsTarget = $this->extractSkillsFromVacancy($job, $skillsUser);
                $seniorityT = $this->inferSeniorityFromText(($job->title ?? '').' '.($job->description ?? ''));
                $faixaT = $this->parseSalaryRange($job->salary_range);
                $modalT = ['location_type' => $job->location_type, 'location' => $job->company?->location];
                $confidence = $this->sinalConfiancaForTarget($job);
                $score = $this->computeScore($skillsUser, $skillsTarget, $seniorityU, $seniorityT, $faixaU, $faixaT, $modalU, $modalT, $confidence);
                if ($score < 0.35) continue;
                $existsRecent = Recommendation::query()
                    ->where('subject_type', $subjectType)
                    ->where('subject_id', $subjectId)
                    ->where('target_type', 'job_vacancy')
                    ->where('target_id', $job->id)
                    ->where('created_at', '>=', Carbon::now()->subDays(7))
                    ->exists();
                if ($existsRecent) continue;
                $scored[] = ['target' => $job, 'score' => $score];
            }
            usort($scored, fn($a,$b) => $b['score'] <=> $a['score']);
            $top = array_slice($scored, 0, $topN);
            foreach ($top as $item) {
                Recommendation::create([
                    'subject_type' => $subjectType,
                    'subject_id' => $subjectId,
                    'target_type' => 'job_vacancy',
                    'target_id' => $item['target']->id,
                    'score' => $item['score'],
                    'batch_date' => $today,
                    'status' => 'pending',
                    'created_at' => Carbon::now(),
                ]);
            }
            return count($top);
        }

        if ($user->isCompany() && $user->company) {
            $subjectType = 'company';
            $subjectId = $user->company->id;
            $query = Freelancer::query()->where('is_active', true);
            $segmentIds = collect($pref->segments ?? [])->filter()->values()->all();
            if (!empty($segmentIds)) { $query->whereIn('segment_id', $segmentIds); }
            $candidates = $query->limit(500)->get();
            $scored = [];
            foreach ($candidates as $freelancer) {
                $skillsTarget = $freelancer->skills()->pluck('name')->all();
                $seniorityT = null;
                $faixaT = ['min' => null, 'max' => null];
                $modalT = ['location' => $freelancer->location, 'location_type' => null];
                $confidence = $this->sinalConfiancaForTarget($freelancer);
                $score = $this->computeScore($skillsUser, $skillsTarget, $seniorityU, $seniorityT, $faixaU, $faixaT, $modalU, $modalT, $confidence);
                if ($score < 0.35) continue;
                $existsRecent = Recommendation::query()
                    ->where('subject_type', $subjectType)
                    ->where('subject_id', $subjectId)
                    ->where('target_type', 'freelancer')
                    ->where('target_id', $freelancer->id)
                    ->where('created_at', '>=', Carbon::now()->subDays(7))
                    ->exists();
                if ($existsRecent) continue;
                $scored[] = ['target' => $freelancer, 'score' => $score];
            }
            usort($scored, fn($a,$b) => $b['score'] <=> $a['score']);
            $top = array_slice($scored, 0, $topN);
            foreach ($top as $item) {
                Recommendation::create([
                    'subject_type' => $subjectType,
                    'subject_id' => $subjectId,
                    'target_type' => 'freelancer',
                    'target_id' => $item['target']->id,
                    'score' => $item['score'],
                    'batch_date' => $today,
                    'status' => 'pending',
                    'created_at' => Carbon::now(),
                ]);
            }
            return count($top);
        }

        return 0;
    }

    /**
     * Próximo card: recommendation pending ordenada por score.
     */
    public function nextCardFor(User $user): ?Recommendation
    {
        return Recommendation::forUser($user)
            ->where('status', 'pending')
            ->orderByDesc('score')
            ->first();
    }

    /**
     * Processa decisão e cria Match quando ambos deram like. Suporta undo.
     */
    public function decide(User $user, int $recommendationId, string $action): array
    {
        $rec = Recommendation::forUser($user)->where('id', $recommendationId)->first();
        if (!$rec) return ['ok' => false, 'match' => false, 'error' => 'recommendation_not_found'];
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
        if (!in_array($action, ['liked','rejected','saved'])) {
            return ['ok' => false, 'match' => false, 'error' => 'invalid_action'];
        }
        $rec->status = $action;
        $rec->decided_at = $now;
        $rec->save();
        $hasMatch = false;
        if ($action === 'liked') {
            if ($rec->target_type === 'job_vacancy' && $rec->subject_type === 'freelancer') {
                $freelancerId = $rec->subject_id;
                $jobId = $rec->target_id;
                $job = JobVacancy::find($jobId);
                if ($job && $job->company) {
                    $companyId = $job->company->id;
                    $inverseLiked = Recommendation::query()
                        ->where('subject_type', 'company')
                        ->where('subject_id', $companyId)
                        ->where('target_type', 'freelancer')
                        ->where('target_id', $freelancerId)
                        ->where('status', 'liked')
                        ->exists();
                    if ($inverseLiked) {
                        Match::firstOrCreate([
                            'freelancer_id' => $freelancerId,
                            'job_vacancy_id' => $jobId,
                        ], ['created_at' => Carbon::now()]);
                        $hasMatch = true;
                    }
                }
            } elseif ($rec->target_type === 'freelancer' && $rec->subject_type === 'company') {
                $companyId = $rec->subject_id;
                $freelancerId = $rec->target_id;
                $jobId = JobVacancy::query()->where('company_id', $companyId)->value('id');
                if ($jobId) {
                    $inverseLiked = Recommendation::query()
                        ->where('subject_type', 'freelancer')
                        ->where('subject_id', $freelancerId)
                        ->where('target_type', 'job_vacancy')
                        ->where('target_id', $jobId)
                        ->where('status', 'liked')
                        ->exists();
                    if ($inverseLiked) {
                        Match::firstOrCreate([
                            'freelancer_id' => $freelancerId,
                            'job_vacancy_id' => $jobId,
                        ], ['created_at' => Carbon::now()]);
                        $hasMatch = true;
                    }
                }
            }
        }
        return ['ok' => true, 'match' => $hasMatch];
    }

    private function extractSkillsFromVacancy(JobVacancy $job, array $referenceSkills): array
    {
        $text = Str::lower(($job->requirements ?? '').' '.($job->description ?? '').' '.($job->title ?? ''));
        $ref = collect($referenceSkills)->map(fn($s) => Str::lower(trim($s)))->filter()->unique()->all();
        $found = [];
        foreach ($ref as $skill) {
            if ($skill === '') continue;
            if (Str::contains($text, $skill)) { $found[] = $skill; }
        }
        if (empty($found)) {
            $names = Skill::pluck('name')->map(fn($n) => Str::lower($n))->all();
            foreach ($names as $n) { if (Str::contains($text, $n)) $found[] = $n; if (count($found) >= 10) break; }
        }
        return $found;
    }

    private function inferSeniorityFromText(string $text): ?int
    {
        $t = Str::lower($text);
        if (Str::contains($t, ['senior','sênior'])) return 3;
        if (Str::contains($t, ['pleno','mid'])) return 2;
        if (Str::contains($t, ['junior','júnior'])) return 1;
        return null;
    }

    private function parseSalaryRange(?string $text): ?array
    {
        if (!$text) return null;
        $digits = [];
        preg_match_all('/(\d+[\.,]?\d*)/u', $text, $m);
        foreach (($m[1] ?? []) as $raw) { $clean = (float)str_replace([',','.'], '', $raw); if ($clean > 0) $digits[] = $clean; }
        if (count($digits) >= 2) { sort($digits); return ['min' => $digits[0], 'max' => $digits[1]]; }
        if (count($digits) === 1) { return ['min' => $digits[0] * 0.9, 'max' => $digits[0] * 1.1]; }
        return null;
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use App\Models\Recommendation;
use App\Models\FreelancerJobRecommendation;
use App\Models\CompanyFreelancerRecommendation;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ConnectController extends Controller
{
    /**
     * Tela principal do módulo Conectar
     */
    public function index(Request $request, RecommendationService $service)
    {
        $user = $request->user();
        $selectedJob = null;
        $companyVacancies = collect();
        $mode = $request->query('mode');
        if (in_array($mode, ['segment', 'all'])) {
            session(['connect_filter' => $mode]);
            session()->forget('connect_cards_shown');
        }

        // Fluxo baseado no perfil ATIVO na sessão
        $activeRole = session('active_role') ?? null;
        if (! $activeRole && $user) {
            if ($user->isFreelancer() && $user->freelancer) {
                $activeRole = 'freelancer';
                session(['active_role' => 'freelancer']);
            } elseif ($user->isCompany() && $user->company) {
                $activeRole = 'company';
                session(['active_role' => 'company']);
            }
        }
        // Empresa: seleção de vaga antes de ver cards, apenas quando active_role === 'company'
        if ($user && $activeRole === 'company' && $user->isCompany() && $user->company) {
            $company = $user->company;
            $selectedJobId = (int) ($request->query('job_id') ?? 0);
            if ($selectedJobId > 0) {
                $selectedJob = JobVacancy::query()
                    ->where('company_id', $company->id)
                    ->where('id', $selectedJobId)
                    ->first();
                if ($selectedJob) {
                    // Persistir contexto da vaga na sessão e preparar recomendações
                    session(['connect_job_id' => $selectedJob->id]);
                    // Resetar contador de cards mostrados ao trocar de vaga
                    session()->forget('connect_cards_shown');
                    $segmentOnly = (session('connect_filter') === 'segment');
                    try {
                        $service->prepareCompanyConnectForJob($user, $selectedJob->id, 50, $segmentOnly);
                    } catch (\Throwable $e) {
                    }
                } else {
                    session()->forget('connect_job_id');
                    // Resetar também caso a vaga informada não pertença à empresa
                    session()->forget('connect_cards_shown');
                }
            } else {
                // Sem seleção: carregar vagas ativas para escolher
                $companyVacancies = $company->vacancies()->active()->latest()->get();
                session()->forget('connect_job_id');
                // Sem vaga no contexto, limpar contador da sessão
                session()->forget('connect_cards_shown');
            }
        } else {
            // Fluxo freelancer ou sem empresa: limpar qualquer contexto de vaga
            session()->forget('connect_job_id');
            session()->forget('connect_cards_shown');
            try {
                $segmentOnly = (session('connect_filter') === 'segment');
                if ($user && $activeRole === 'freelancer' && $user->isFreelancer() && $user->freelancer) {
                    $service->generateDailyBatchFor($user, 50, $segmentOnly);
                }
            } catch (\Throwable $e) {
            }
        }

        // Notificações de novos matches desde a última visita
        $lastSeen = session('connect_last_match_seen');
        $now = now();
        $matchNotice = [];
        $userMatches = [];
        if (($activeRole === 'freelancer') && $user->isFreelancer() && $user->freelancer) {
            $fid = $user->freelancer->id;
            $query = DB::table('matches')
                ->join('job_vacancies', 'matches.job_vacancy_id', '=', 'job_vacancies.id')
                ->leftJoin('companies', 'job_vacancies.company_id', '=', 'companies.id')
                ->select('matches.*', 'job_vacancies.title as job_title', 'job_vacancies.id as job_id', 'companies.display_name as company_name')
                ->where('matches.freelancer_id', $fid)
                ->orderByDesc('matches.created_at');
            if ($lastSeen) {
                $new = (clone $query)->where('matches.created_at', '>', $lastSeen)->get();
                foreach ($new as $m) {
                    $matchNotice[] = [
                        'text' => 'Match com a vaga "'.$m->job_title.'"'.($m->company_name ? ' da '.$m->company_name : ''),
                        'url' => route('vagas.show', $m->job_id),
                    ];
                }
            }
            $userMatches = $query->limit(20)->get();
        } elseif (($activeRole === 'company') && $user->isCompany() && $user->company) {
            $cid = $user->company->id;
            $query = DB::table('matches')
                ->join('job_vacancies', 'matches.job_vacancy_id', '=', 'job_vacancies.id')
                ->join('freelancers', 'matches.freelancer_id', '=', 'freelancers.id')
                ->join('users', 'freelancers.user_id', '=', 'users.id')
                ->select('matches.*', 'job_vacancies.title as job_title', 'job_vacancies.id as job_id', 'freelancers.display_name as freelancer_name', 'users.id as user_id')
                ->where('job_vacancies.company_id', $cid)
                ->orderByDesc('matches.created_at');
            if ($lastSeen) {
                $new = (clone $query)->where('matches.created_at', '>', $lastSeen)->get();
                foreach ($new as $m) {
                    $matchNotice[] = [
                        'text' => 'Match com o freelancer "'.$m->freelancer_name.'" na vaga "'.$m->job_title.'"',
                        'url' => route('profiles.show', $m->user_id),
                    ];
                }
            }
            $userMatches = $query->limit(20)->get();
        }
        session(['connect_last_match_seen' => $now]);
        $initialCard = null;
        try {
            $candidate = $service->nextCardFor($user);
            if (! $candidate) {
                $activeRole = session('active_role');
                if ($activeRole === 'company' && $user->isCompany() && session()->has('connect_job_id')) {
                    $jobId = (int) session('connect_job_id');
                    if ($jobId > 0) {
                        $segmentOnly = (session('connect_filter') === 'segment');
                        $service->prepareCompanyConnectForJob($user, $jobId, 50, $segmentOnly);
                        $candidate = $service->nextCardFor($user);
                    }
                } else {
                    $segmentOnly = (session('connect_filter') === 'segment');
                    $service->generateDailyBatchFor($user, 50, $segmentOnly);
                    $candidate = $service->nextCardFor($user);
                }
            }
            if ($candidate instanceof \App\Models\FreelancerJobRecommendation) {
                $candidate->load(['job.company']);
            } elseif ($candidate instanceof \App\Models\CompanyFreelancerRecommendation) {
                $candidate->load(['freelancer.user', 'freelancer.skills']);
            }
            if ($candidate) {
                $initialCard = $this->mapRecommendationToCard($candidate);
            }
        } catch (\Throwable $e) {
        }

        return view('connect.index', [
            'selectedJob' => $selectedJob,
            'companyVacancies' => $companyVacancies,
            'matchNotice' => $matchNotice,
            'userMatches' => $userMatches,
            'newMatchesCount' => is_array($matchNotice) ? count($matchNotice) : 0,
            'initialCard' => $initialCard,
        ]);
    }

    /**
     * Retorna JSON do próximo card real a partir de recommendations pendentes.
     */
    public function next(Request $request, RecommendationService $service)
    {
        // Limite por sessão: 20 cards
        $count = (int) session()->get('connect_cards_shown', 0);
        if ($count >= 20) {
            return response()->json(['done' => true], 204);
        }

        $user = $request->user();
        $rec = $service->nextCardFor($user);
        // Fallback: se não houver batch gerado ainda, tenta gerar agora e buscar novamente
        if (! $rec) {
            try {
                $activeRole = session('active_role');
                if ($activeRole === 'company' && $user->isCompany() && session()->has('connect_job_id')) {
                    $jobId = (int) session('connect_job_id');
                    if ($jobId > 0) {
                        $segmentOnly = (session('connect_filter') === 'segment');
                        $service->prepareCompanyConnectForJob($user, $jobId, 50, $segmentOnly);
                    }
                } else {
                    $segmentOnly = (session('connect_filter') === 'segment');
                    $service->generateDailyBatchFor($user, 50, $segmentOnly);
                }
            } catch (\Throwable $e) {
                // ignora e segue
            }
            $rec = $service->nextCardFor($user);
        }
        if (! $rec) {
            return response()->json(['empty' => true], 204);
        }

        try {
            if ($rec instanceof \App\Models\FreelancerJobRecommendation) {
                $rec->load(['job.company']);
            } elseif ($rec instanceof \App\Models\CompanyFreelancerRecommendation) {
                $rec->load(['freelancer.user']);
            }
        } catch (\Throwable $e) {}

        // Incrementa contador de sessão
        session()->put('connect_cards_shown', $count + 1);

        return response()->json($this->mapRecommendationToCard($rec));
    }

    /**
     * Recebe uma decisão do usuário e aplica na recommendation; pode retornar {match:true}.
     */
    public function decide(Request $request, RecommendationService $service)
    {
        $validated = $request->validate([
            'recommendation_id' => ['required', 'integer'],
            'action' => ['required', 'in:liked,rejected,saved,undo'],
            'job_vacancy_id' => ['nullable', 'integer'],
        ]);

        $user = $request->user();
        $jobContext = isset($validated['job_vacancy_id']) ? (int) $validated['job_vacancy_id'] : (int) session('connect_job_id');
        $result = $service->decide($user, (int) $validated['recommendation_id'], $validated['action'], $jobContext > 0 ? $jobContext : null);

        return response()->json($result);
    }

    private function mapRecommendationToCard($rec): array
    {
        if ($rec instanceof FreelancerJobRecommendation) {
            $job = $rec->job ?: \App\Models\JobVacancy::with('company')->find($rec->job_vacancy_id);
            $companyName = $job?->company?->display_name ?: ($job?->company?->name ?: 'Empresa');

            return [
                'id' => $rec->id,
                'type' => 'job',
                'score' => round((float) $rec->score, 2),
                'payload' => [
                    'id' => $job?->id,
                    'title' => $job?->title ?: 'Vaga',
                    'company' => $companyName,
                    'location' => ($job && $job->company && $job->company->location) ? $job->company->location : '-',
                    'mode' => $job?->location_type ?: '-',
                    'range' => ($job && $job->salary_min && $job->salary_max) ? ('R$ '.number_format((float)$job->salary_min, 2, ',', '.').' - R$ '.number_format((float)$job->salary_max, 2, ',', '.')) : '-',
                    'skills' => [],
                    'summary' => Str::limit($job?->description ?: '', 180),
                    'job_url' => $job ? route('vagas.show', $job->id) : null,
                ],
            ];
        }

        if ($rec instanceof CompanyFreelancerRecommendation) {
            $f = $rec->freelancer ?: \App\Models\Freelancer::with(['user'])->find($rec->freelancer_id);
            $skills = [];

            return [
                'id' => $rec->id,
                'type' => 'freelancer',
                'score' => round((float) $rec->score, 2),
                'payload' => [
                    'id' => $f?->id,
                    'title' => $f?->display_name ?: 'Freelancer',
                    'location' => ($f && $f->location) ? $f->location : '-',
                    'mode' => '—',
                    'range' => ($f && $f->hourly_rate) ? ('R$ '.number_format((float) $f->hourly_rate, 2, ',', '.').'/h') : '-',
                    'skills' => $skills,
                    'summary' => Str::limit($f?->bio ?: '', 180),
                    'profile_url' => ($f && $f->user) ? route('profiles.show', $f->user) : null,
                ],
            ];
        }

        return [
            'id' => $rec->id,
            'type' => 'unknown',
            'score' => round((float) $rec->score, 2),
            'payload' => ['title' => '—'],
        ];
    }
}

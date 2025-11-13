<?php

namespace App\Http\Controllers;

use App\Models\Freelancer;
use App\Models\JobVacancy;
use App\Models\Recommendation;
use Illuminate\Http\Request;

class MatchmakingController extends Controller
{
    public function freelancer(Request $request)
    {
        $user = $request->user();
        if (! $user || ! $user->isFreelancer() || ! $user->freelancer) {
            return redirect()->route('dashboard')->with('error', 'Perfil de freelancer não encontrado.');
        }

        $freelancer = $user->freelancer;
        $segmentIds = $freelancer->segments()->pluck('segments.id')->all();

        $jobs = JobVacancy::query()
            ->active()
            ->where(function ($q) use ($segmentIds) {
                if ($segmentIds) {
                    $q->whereHas('category', function ($cq) use ($segmentIds) {
                        $cq->whereIn('segment_id', $segmentIds);
                    })
                        ->orWhereHas('company', function ($compQ) use ($segmentIds) {
                            $compQ->whereIn('segment_id', $segmentIds);
                        });
                }
            })
            ->with('company')
            ->latest()
            ->paginate(12);

        return view('matchmaking.freelancer', compact('jobs'));
    }

    public function company(Request $request)
    {
        $user = $request->user();
        if (! $user || ! $user->isCompany() || ! $user->company) {
            return redirect()->route('dashboard')->with('error', 'Perfil de empresa não encontrado.');
        }

        $company = $user->company;
        $companyVacancies = $company->vacancies()->active()->latest()->get();
        $selectedId = (int) $request->query('job_id', 0);
        $freelancers = collect();

        if ($selectedId > 0) {
            $job = $company->vacancies()->active()->where('id', $selectedId)->with('category')->first();
            if ($job) {
                $segmentIds = [];
                if ($job->category_id && $job->category) {
                    $segmentIds[] = (int) $job->category->segment_id;
                }
                // Fallback: segmentos da empresa
                $segmentIds = array_unique(array_filter(array_merge($segmentIds, $company->segments()->pluck('segments.id')->all())));

                if ($segmentIds) {
                    $freelancers = Freelancer::query()
                        ->where('is_active', true)
                        ->whereHas('segments', function ($q) use ($segmentIds) {
                            $q->whereIn('segments.id', $segmentIds);
                        })
                        ->with('user')
                        ->latest()
                        ->paginate(12);
                }
            }
        }

        return view('matchmaking.company', compact('companyVacancies', 'selectedId', 'freelancers'));
    }

    public function saveJob(Request $request)
    {
        $user = $request->user();
        if (! $user || ! $user->isFreelancer() || ! $user->freelancer) {
            return response()->json(['success' => false, 'message' => 'Apenas freelancers podem salvar vagas.'], 403);
        }

        $validated = $request->validate([
            'job_id' => ['required', 'integer', 'exists:job_vacancies,id'],
        ]);

        $freelancerId = $user->freelancer->id;
        $rec = Recommendation::firstOrCreate([
            'subject_type' => 'freelancer',
            'subject_id' => $freelancerId,
            'target_type' => 'job',
            'target_id' => (int) $validated['job_id'],
        ], [
            'score' => 0,
            'batch_date' => now()->toDateString(),
            'created_at' => now(),
        ]);

        $rec->status = 'saved';
        $rec->decided_at = now();
        $rec->save();

        return response()->json(['success' => true]);
    }
}

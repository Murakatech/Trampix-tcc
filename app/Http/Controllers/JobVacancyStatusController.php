<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class JobVacancyStatusController extends Controller
{
    /**
     * Exibe a página de status da vaga.
     * Apenas a empresa dona da vaga e freelancers aceitos podem ver.
     */
    public function show(JobVacancy $vaga)
    {
        // Carregar relações necessárias
        $vaga->load(['company.user', 'applications.freelancer.user']);

        $user = Auth::user();
        $isCompanyOwner = $user?->company && $vaga->company_id === $user->company->id;
        $acceptedApplications = $vaga->applications->where('status', 'accepted');
        $endedApplications = $vaga->applications->where('status', 'ended');
        $isAcceptedFreelancer = $user?->freelancer && (
            $acceptedApplications->contains(fn($a) => $a->freelancer_id === $user->freelancer->id) ||
            $endedApplications->contains(fn($a) => $a->freelancer_id === $user->freelancer->id)
        );
        $currentApplication = null;
        if ($user?->freelancer) {
            $currentApplication = $vaga->applications->firstWhere('freelancer_id', $user->freelancer->id);
        }

        if (!$isCompanyOwner && !$isAcceptedFreelancer) {
            abort(403, 'Acesso negado.');
        }

        return view('vagas.status', [
            'vaga' => $vaga,
            'acceptedApplications' => $acceptedApplications,
            'endedApplications' => $endedApplications,
            'isCompanyOwner' => $isCompanyOwner,
            'isAcceptedFreelancer' => $isAcceptedFreelancer,
            'currentApplication' => $currentApplication,
        ]);
    }
}
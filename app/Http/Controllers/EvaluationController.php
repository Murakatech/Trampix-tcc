<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    /**
     * Exibe o formulário de avaliação pós-contrato.
     * Acesso: empresa dona da vaga OU freelancer da aplicação.
     * Requisito: parceria encerrada (status != 'accepted').
     */
    public function create(Application $application)
    {
        $user = Auth::user();

        $isCompanyOwner = $user?->company && $application->jobVacancy && $application->jobVacancy->company_id === $user->company->id;
        $isFreelancerOwner = $user?->freelancer && $application->freelancer_id === $user->freelancer->id;

        if (!$isCompanyOwner && !$isFreelancerOwner) {
            abort(403, 'Acesso negado.');
        }

        if ($application->status === 'accepted') {
            return redirect()->back()->with('error', 'A avaliação só pode ser feita após finalizar o contrato.');
        }

        $application->load(['jobVacancy.company.user', 'freelancer.user']);

        return view('evaluations.form', [
            'application' => $application,
            'isCompanyOwner' => $isCompanyOwner,
            'isFreelancerOwner' => $isFreelancerOwner,
        ]);
    }

    /**
     * Recebe e valida a avaliação. Por enquanto, apenas exibe sucesso (sem persistência).
     */
    public function store(Request $request, Application $application)
    {
        $user = Auth::user();
        $isCompanyOwner = $user?->company && $application->jobVacancy && $application->jobVacancy->company_id === $user->company->id;
        $isFreelancerOwner = $user?->freelancer && $application->freelancer_id === $user->freelancer->id;

        if (!$isCompanyOwner && !$isFreelancerOwner) {
            abort(403, 'Acesso negado.');
        }

        if ($application->status === 'accepted') {
            return redirect()->back()->with('error', 'A avaliação só pode ser feita após finalizar o contrato.');
        }

        // Validação simples de 5 a 10 perguntas com nota 1-5
        $validated = $request->validate([
            'ratings' => ['required','array','min:5','max:10'],
            'ratings.*' => ['required','integer','between:1,5'],
            'comments' => ['nullable','string','max:1000'],
        ]);

        // Calcula média
        $ratings = collect($validated['ratings']);
        $average = round($ratings->avg(), 2);

        // Por enquanto, sem persistir — apenas feedback visual
        return redirect()->route('vagas.status', $application->job_vacancy_id)
            ->with('success', 'Avaliação enviada com média '.$average.'. Em breve será consolidada no perfil.');
    }
}
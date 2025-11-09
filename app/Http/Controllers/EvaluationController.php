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
     * Recebe e valida a avaliação e PERSISTE no registro da aplicação.
     */
    public function store(Request $request, Application $application)
    {
        $user = Auth::user();
        $isCompanyOwner = $user?->company && $application->jobVacancy && $application->jobVacancy->company_id === $user->company->id;
        $isFreelancerOwner = $user?->freelancer && $application->freelancer_id === $user->freelancer->id;

        if (!$isCompanyOwner && !$isFreelancerOwner) {
            abort(403, 'Acesso negado.');
        }

        // Permite avaliar se a parceria foi finalizada (status 'ended').
        // Também permitimos avaliar caso exista fluxo legado que permita avaliação com status diferente, desde que não seja 'accepted'.
        if ($application->status === 'accepted') {
            return redirect()->back()->with('error', 'A avaliação só pode ser feita após finalizar o contrato.');
        }

        // Validação de 5 a 10 perguntas com nota 1-5, consolidando em uma média
        $validated = $request->validate([
            'ratings' => ['required','array','min:3','max:10'],
            'ratings.*' => ['required','integer','between:1,5'],
            'comments' => ['nullable','string','max:1000'],
        ]);

        // Calcula média arredondada para inteiro (1-5)
        $average = (int) round(collect($validated['ratings'])->avg());

        if ($isCompanyOwner) {
            // Empresa avalia o freelancer
            $application->company_rating = $average;
            $application->company_comment = $validated['comments'] ?? null;
            $application->evaluated_by_company_at = now();
        } else {
            // Freelancer avalia a empresa
            $application->freelancer_rating = $average;
            $application->freelancer_comment = $validated['comments'] ?? null;
            $application->evaluated_by_freelancer_at = now();
        }

        $application->save();

        // Após avaliar, encaminhamos para Trabalhos Finalizados
        return redirect()->route('finished.index')
            ->with('success', 'Avaliação enviada com sucesso. Obrigado pelo feedback!');
    }
}
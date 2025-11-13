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
     * Requisito: contrato finalizado (status === 'ended').
     */
    public function create(Application $application)
    {
        $user = Auth::user();

        $isCompanyOwner = $user?->company && $application->jobVacancy && $application->jobVacancy->company_id === $user->company->id;
        $isFreelancerOwner = $user?->freelancer && $application->freelancer_id === $user->freelancer->id;

        if (! $isCompanyOwner && ! $isFreelancerOwner) {
            abort(403, 'Acesso negado.');
        }

        // Apenas contratos finalizados podem ser avaliados
        if ($application->status !== 'ended') {
            return redirect()->back()->with('error', 'A avaliação só pode ser feita em contratos finalizados.');
        }

        $application->load(['jobVacancy.company.user', 'freelancer.user']);

        // Carregar respostas existentes (para edição) conforme papel
        $existingRatings = [];
        $existingComment = null;
        if ($isCompanyOwner) {
            $existingRatings = is_array($application->company_ratings_json) ? $application->company_ratings_json : (json_decode($application->company_ratings_json ?? '[]', true) ?: []);
            $existingComment = $application->company_comment;
        } elseif ($isFreelancerOwner) {
            $existingRatings = is_array($application->freelancer_ratings_json) ? $application->freelancer_ratings_json : (json_decode($application->freelancer_ratings_json ?? '[]', true) ?: []);
            $existingComment = $application->freelancer_comment;
        }

        return view('evaluations.form', [
            'application' => $application,
            'isCompanyOwner' => $isCompanyOwner,
            'isFreelancerOwner' => $isFreelancerOwner,
            'existingRatings' => $existingRatings,
            'existingComment' => $existingComment,
        ]);
    }

    /**
     * Recebe e valida a avaliação e PERSISTE no registro da aplicação.
     * Requisito: contrato finalizado (status === 'ended').
     */
    public function store(Request $request, Application $application)
    {
        $user = Auth::user();
        $isCompanyOwner = $user?->company && $application->jobVacancy && $application->jobVacancy->company_id === $user->company->id;
        $isFreelancerOwner = $user?->freelancer && $application->freelancer_id === $user->freelancer->id;

        if (! $isCompanyOwner && ! $isFreelancerOwner) {
            abort(403, 'Acesso negado.');
        }

        // Apenas contratos finalizados podem ser avaliados
        if ($application->status !== 'ended') {
            return redirect()->back()->with('error', 'A avaliação só pode ser feita em contratos finalizados.');
        }

        // Validação de 5 a 10 perguntas com nota 1-5, consolidando em uma média
        $validated = $request->validate([
            'ratings' => ['required', 'array', 'min:3', 'max:10'],
            'ratings.*' => ['required', 'integer', 'between:1,5'],
            'comments' => ['nullable', 'string', 'max:1000'],
        ]);

        // Calcula média com uma casa decimal (1-5)
        $avgDecimal = round(collect($validated['ratings'])->avg(), 1);
        $avgInt = (int) round($avgDecimal);

        if ($isCompanyOwner) {
            // Empresa avalia o freelancer
            $application->company_rating = $avgInt; // compatibilidade
            $application->company_rating_avg = $avgDecimal;
            $application->company_comment = $validated['comments'] ?? null;
            $application->company_ratings_json = $validated['ratings'];
            $application->evaluated_by_company_at = now();
        } else {
            // Freelancer avalia a empresa
            $application->freelancer_rating = $avgInt; // compatibilidade
            $application->freelancer_rating_avg = $avgDecimal;
            $application->freelancer_comment = $validated['comments'] ?? null;
            $application->freelancer_ratings_json = $validated['ratings'];
            $application->evaluated_by_freelancer_at = now();
        }

        $application->save();

        // Após avaliar, encaminhamos para Trabalhos Finalizados
        return redirect()->route('finished.index')
            ->with('success', 'Avaliação enviada com sucesso. Obrigado pelo feedback!');
    }

    /**
     * Exibe a avaliação completa (todas as respostas e comentários) da aplicação.
     * Acesso: empresa dona da vaga OU freelancer da aplicação.
     */
    public function show(Application $application)
    {
        $user = Auth::user();
        $isCompanyOwner = $user?->company && $application->jobVacancy && $application->jobVacancy->company_id === $user->company->id;
        $isFreelancerOwner = $user?->freelancer && $application->freelancer_id === $user->freelancer->id;

        if (! $isCompanyOwner && ! $isFreelancerOwner) {
            abort(403, 'Acesso negado.');
        }

        $application->load(['jobVacancy.company.user', 'freelancer.user']);

        return view('evaluations.show', [
            'application' => $application,
            'isCompanyOwner' => $isCompanyOwner,
            'isFreelancerOwner' => $isFreelancerOwner,
        ]);
    }
}

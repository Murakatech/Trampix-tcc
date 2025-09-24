<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class JobVacancyController extends Controller
{
    // Lista pública de vagas
    public function index()
    {
        $vagas = JobVacancy::latest()->paginate(10);
        return view('vagas.index', compact('vagas'));
    }

    // Form de criação (apenas empresa)
    public function create()
    {
        if (! Gate::allows('isCompany')) abort(403);
        return view('vagas.create');
    }

    // Salva vaga (apenas empresa)
    public function store(Request $req)
    {
        if (! Gate::allows('isCompany')) abort(403);

        $data = $req->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'contract_type' => 'nullable|in:PJ,CLT,Estágio,Freelance',
            'location_type' => 'nullable|in:Remoto,Híbrido,Presencial',
            'salary_range' => 'nullable|string|max:100',
        ]);

        $company = Company::firstOrCreate(
            ['user_id' => auth()->id()],
            ['name' => auth()->user()->name]
        );

        $data['company_id'] = $company->id;

        $vaga = JobVacancy::create($data);

        return redirect()->route('vagas.show', $vaga)->with('ok', 'Vaga criada.');
    }

    // Exibe uma vaga
    public function show(JobVacancy $vaga)
    {
        return view('vagas.show', compact('vaga'));
    }

    // Form de edição (apenas dona da vaga)
    public function edit(JobVacancy $vaga)
    {
        if (! Gate::allows('isCompany') || $vaga->company->user_id !== auth()->id()) abort(403);
        return view('vagas.edit', compact('vaga'));
    }

    // Atualiza vaga
    public function update(Request $req, JobVacancy $vaga)
    {
        if (! Gate::allows('isCompany') || $vaga->company->user_id !== auth()->id()) abort(403);

        $data = $req->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'contract_type' => 'nullable|in:PJ,CLT,Estágio,Freelance',
            'location_type' => 'nullable|in:Remoto,Híbrido,Presencial',
            'salary_range' => 'nullable|string|max:100',
            'status' => 'nullable|in:active,closed',
        ]);

        $vaga->update($data);

        return redirect()->route('vagas.show', $vaga)->with('ok', 'Vaga atualizada.');
    }

    // Exclui vaga
    public function destroy(JobVacancy $vaga)
    {
        if (! Gate::allows('isCompany') || $vaga->company->user_id !== auth()->id()) abort(403);
        $vaga->delete();
        return redirect()->route('vagas.index')->with('ok', 'Vaga removida.');
    }
}

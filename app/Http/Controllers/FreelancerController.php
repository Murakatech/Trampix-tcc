<?php

namespace App\Http\Controllers;

use App\Models\Freelancer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class FreelancerController extends Controller
{
    public function index()
    {
        Gate::authorize('isAdmin');
        
        $freelancers = Freelancer::with('user')
            ->where('is_active', true)
            ->paginate(15);
            
        return view('freelancers.index', compact('freelancers'));
    }

    public function show(Freelancer $freelancer)
    {
        // Só o dono do perfil ou admin podem ver
        if (auth()->user()->isAdmin() || 
            (auth()->user()->freelancer && auth()->user()->freelancer->id === $freelancer->id)) {
            return view('freelancers.show', compact('freelancer'));
        }

        abort(403, 'Acesso negado. Você só pode visualizar seu próprio perfil.');
    }

    public function showOwn()
    {
        Gate::authorize('isFreelancer');
        
        $freelancer = auth()->user()->freelancer;
        
        if (!$freelancer) {
            return redirect()->route('freelancers.create')
                ->with('info', 'Você precisa criar um perfil de freelancer primeiro.');
        }
        
        return view('freelancers.show', compact('freelancer'));
    }

    public function create()
    {
        Gate::authorize('canCreateFreelancerProfile');
        
        return view('freelancers.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('canCreateFreelancerProfile');
        
        $validated = $request->validate([
            'bio' => 'nullable|string|max:1000',
            'portfolio_url' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:100',
            'hourly_rate' => 'nullable|numeric|min:0|max:999999.99',
            'availability' => 'nullable|string|max:255',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        // Upload do CV se fornecido
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cvs', 'public');
            $validated['cv_url'] = $cvPath;
        }

        // Criar perfil freelancer
        $freelancer = auth()->user()->createProfile('freelancer', $validated);

        return redirect()->route('freelancers.edit', $freelancer)
            ->with('success', 'Perfil de freelancer criado com sucesso!');
    }

    public function edit(Freelancer $freelancer)
    {
        Gate::authorize('editFreelancerProfile', $freelancer);
        
        return view('freelancers.edit', compact('freelancer'));
    }

    public function update(Request $request, Freelancer $freelancer)
    {
        Gate::authorize('editFreelancerProfile', $freelancer);
        
        $validated = $request->validate([
            'bio' => 'nullable|string|max:1000',
            'portfolio_url' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:100',
            'hourly_rate' => 'nullable|numeric|min:0|max:999999.99',
            'availability' => 'nullable|string|max:255',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'remove_cv' => 'nullable|boolean',
        ]);

        // Remover CV se solicitado
        if ($request->boolean('remove_cv') && $freelancer->cv_url) {
            Storage::disk('public')->delete($freelancer->cv_url);
            $validated['cv_url'] = null;
        }

        // Upload do novo CV se fornecido
        if ($request->hasFile('cv')) {
            // Remover CV antigo
            if ($freelancer->cv_url) {
                Storage::disk('public')->delete($freelancer->cv_url);
            }
            
            $cvPath = $request->file('cv')->store('cvs', 'public');
            $validated['cv_url'] = $cvPath;
        }

        $freelancer->update($validated);

        return redirect()->route('freelancers.edit', $freelancer)
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    public function destroy(Freelancer $freelancer)
    {
        Gate::authorize('editFreelancerProfile', $freelancer);
        
        // Remover CV do storage
        if ($freelancer->cv_url) {
            Storage::disk('public')->delete($freelancer->cv_url);
        }

        // Soft delete - marcar como inativo
        $freelancer->update(['is_active' => false]);

        return redirect()->route('profile.edit')
            ->with('success', 'Perfil de freelancer desativado com sucesso!');
    }

    public function downloadCv(Freelancer $freelancer)
    {
        if (!$freelancer->cv_url || !Storage::disk('public')->exists($freelancer->cv_url)) {
            abort(404, 'CV não encontrado');
        }

        return Storage::disk('public')->download($freelancer->cv_url, 'CV_' . $freelancer->user->name . '.pdf');
    }
}

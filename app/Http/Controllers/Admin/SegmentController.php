<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Segment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class SegmentController extends Controller
{
    public function store(Request $request)
    {
        if (!Gate::allows('isAdmin')) abort(403);

        $data = $request->validate([
            'name' => ['required','string','max:255','unique:segments,name'],
        ]);

        $segment = Segment::create(['name' => $data['name']]);

        // Invalidar cache dos filtros de vagas
        Cache::forget('vagas_filter_data');

        return redirect()->route('admin.categories.index')
            ->with('ok', 'Segmento "'.$segment->name.'" criado com sucesso.');
    }

    public function update(Request $request, Segment $segment)
    {
        if (!Gate::allows('isAdmin')) abort(403);

        $data = $request->validate([
            'name' => ['required','string','max:255', Rule::unique('segments','name')->ignore($segment->id)],
        ]);

        $segment->name = $data['name'];
        $segment->save();

        Cache::forget('vagas_filter_data');

        return redirect()->route('admin.categories.index')
            ->with('ok', 'Segmento "'.$segment->name.'" atualizado com sucesso.');
    }

    public function deactivate(Segment $segment)
    {
        if (!Gate::allows('isAdmin')) abort(403);
        $segment->active = false;
        $segment->save();
        Cache::forget('vagas_filter_data');
        return redirect()->route('admin.categories.index')
            ->with('ok', "Segmento \"{$segment->name}\" desativado.");
    }

    public function reactivate(Segment $segment)
    {
        if (!Gate::allows('isAdmin')) abort(403);
        $segment->active = true;
        $segment->save();
        Cache::forget('vagas_filter_data');
        return redirect()->route('admin.categories.index')
            ->with('ok', "Segmento \"{$segment->name}\" reativado.");
    }

    public function destroy(Segment $segment)
    {
        if (!Gate::allows('isAdmin')) abort(403);
        // Removido: exclusão permanente. Mantemos registros para evitar quebra de dados.
        return redirect()->route('admin.categories.index')
            ->with('error', 'A exclusão permanente de segmentos foi desativada. Utilize "Desativar" para torná-lo indisponível.');
    }
}
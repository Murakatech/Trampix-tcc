<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Segment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        if (! Gate::allows('isAdmin')) {
            abort(403);
        }

        $categoriesActive = Category::with('segment')
            ->where('active', true)
            ->orderBy('name')
            ->paginate(20, ['*'], 'categories_active');
        $categoriesInactive = Category::with('segment')
            ->where('active', false)
            ->orderBy('name')
            ->get();
        $segmentsActive = Segment::where('active', true)->orderBy('name')->get();
        $segmentsInactive = Segment::where('active', false)->orderBy('name')->get();
        $stats = [
            'total' => Category::count(),
            'segments_total' => Segment::count(),
            'active_total' => Category::where('active', true)->count(),
            'inactive_total' => Category::where('active', false)->count(),
        ];

        return view('admin.categories.index', [
            'categoriesActive' => $categoriesActive,
            'categoriesInactive' => $categoriesInactive,
            'segmentsActive' => $segmentsActive,
            'segmentsInactive' => $segmentsInactive,
            'stats' => $stats,
        ]);
    }

    public function store(Request $request)
    {
        if (! Gate::allows('isAdmin')) {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'segment_id' => ['nullable', 'integer', 'exists:segments,id'],
        ]);

        $category = Category::create([
            'name' => $data['name'],
            'slug' => \Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'segment_id' => $data['segment_id'] ?? null,
        ]);

        // Invalidar cache dos filtros de vagas para refletir nova categoria
        Cache::forget('vagas_filter_data');

        return redirect()->route('admin.categories.index')
            ->with('ok', 'Categoria "'.$category->name.'" criada com sucesso.');
    }

    public function update(Request $request, Category $category)
    {
        if (! Gate::allows('isAdmin')) {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'segment_id' => ['nullable', 'integer', 'exists:segments,id'],
        ]);

        $category->name = $data['name'];
        $category->slug = \Str::slug($data['name']);
        $category->description = $data['description'] ?? null;
        $category->segment_id = $data['segment_id'] ?? null;
        $category->save();

        Cache::forget('vagas_filter_data');

        return redirect()->route('admin.categories.index')
            ->with('ok', 'Categoria "'.$category->name.'" atualizada com sucesso.');
    }

    public function deactivate(Category $category)
    {
        if (! Gate::allows('isAdmin')) {
            abort(403);
        }
        $category->active = false;
        $category->save();
        Cache::forget('vagas_filter_data');

        return redirect()->route('admin.categories.index')
            ->with('ok', "Categoria \"{$category->name}\" desativada.");
    }

    public function reactivate(Category $category)
    {
        if (! Gate::allows('isAdmin')) {
            abort(403);
        }
        $category->active = true;
        $category->save();
        Cache::forget('vagas_filter_data');

        return redirect()->route('admin.categories.index')
            ->with('ok', "Categoria \"{$category->name}\" reativada.");
    }

    public function destroy(Category $category)
    {
        if (! Gate::allows('isAdmin')) {
            abort(403);
        }

        // Opcionalmente: impedir exclusão se houver vagas associadas
        // if ($category->jobVacancies()->exists()) {
        //     return redirect()->route('admin.categories.index')
        //         ->with('error', 'Não é possível excluir a categoria, existem vagas vinculadas.');
        // }

        // Removido: exclusão permanente. Mantemos registros para evitar quebra de dados.
        return redirect()->route('admin.categories.index')
            ->with('error', 'A exclusão permanente de categorias foi desativada. Utilize "Desativar" para torná-la indisponível.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index()
    {
        if (!Gate::allows('isAdmin')) abort(403);

        $categories = Category::orderBy('name')->paginate(20);
        $stats = [
            'total' => Category::count(),
        ];

        return view('admin.categories.index', compact('categories', 'stats'));
    }

    public function store(Request $request)
    {
        if (!Gate::allows('isAdmin')) abort(403);

        $data = $request->validate([
            'name' => ['required','string','max:255','unique:categories,name'],
            'description' => ['nullable','string','max:1000'],
        ]);

        $category = Category::create([
            'name' => $data['name'],
            'slug' => \Str::slug($data['name']),
            'description' => $data['description'] ?? null,
        ]);

        // Invalidar cache dos filtros de vagas para refletir nova categoria
        Cache::forget('vagas_filter_data');

        return redirect()->route('admin.categories.index')
            ->with('ok', 'Categoria "'.$category->name.'" criada com sucesso.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        $stylesheet = '';
        return view('categories.index', compact('categories', 'stylesheet'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $stylesheet = '';
        return view('categories.create', compact('stylesheet'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
        ]);

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $stylesheet = '';
        return view('categories.edit', compact('category', 'stylesheet'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Verificar se a categoria está sendo usada por algum agente
        if ($category->agents()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Esta categoria não pode ser excluída porque está sendo usada por um ou mais agentes.');
        }

        $category->delete();
        return redirect()->route('categories.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }
}

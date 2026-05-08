<?php

namespace App\Http\Controllers;

use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PublisherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $publishers = Publisher::query()
        ->when($request->search, function($query, $search) {
            $query->where('name', 'like', "%{$search}%");
        })
            ->withCount('books')
            ->when($request->sort, function($query, $sort) {
                if ($sort === 'total_livros_asc') $query->orderBy('total_livros', 'asc');
                if ($sort === 'total_livros_desc') $query->orderBy('total_livros', 'desc');
            }, function ($query) {
            $query->latest();})
            ->paginate(15, ['*'], 'pag')
            ->withQueryString();

        return Inertia::render('Publishers/Index', [
            'publishers' => $publishers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Publishers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo_path' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('logo_path')) {
            $path = $request->file('logo_path')->store('editoras', 'public');
        }

        Publisher::create([
            'name' => $validated['name'],
            'logo_path' => $path ? '/media/' . $path : null,
        ]);

        return redirect()->route('editoras.index')->with('success', 'Autor adicionado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Publisher $editora)
    {
        return Inertia::render('Publishers/Show', [
            'publisher' => $editora,
            'books' => $editora->books()->with('publisher')->get()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Publisher $editora)
    {
        return Inertia::render('Publishers/Edit', [
            'publisher' => $editora,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Publisher $editora)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo_path' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        $finalPath = $editora->logo_path;

        // 1. Apagar a imagem antiga e guardar a nova imagem
        if ($request->hasFile('logo_path')) {
            if ($editora->logo_path && !str_contains($editora->logo_path, 'http')) {
                Storage::disk('public')->delete(str_replace('/media/', '', $editora->logo_path));
            }

            $path = $request->file('logo_path')->store('editoras', 'public');
            $finalPath = '/media/' . $path;
        }

        $editora->update([
            'name' => $validated['name'],
            'logo_path' => $finalPath,
        ]);

        return redirect()->route('editoras.show', $editora->id)->with('success', 'Autor atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publisher $editora)
    {
        if ($editora->logo_path && !str_contains($editora->logo_path, 'http')) {
            $path = str_replace('/media/', '', $editora->logo_path);
            Storage::disk('public')->delete($path);
        }

        $editora->delete();
        return redirect()->route('editoras.index')->with('success', 'Autor removido com sucesso!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Exports\AuthorsExport;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $authors = Author::query()
            ->when($request->search, function($query, $search){
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->withCount('books')
            ->when($request->sort, function($query, $sort){
                match ($sort) {
                    'nome_az'          => $query->orderBy('name', 'asc'),
                    'nome_za'          => $query->orderBy('name', 'desc'),
                    'livros_asc'       => $query->orderBy('books_count', 'asc'),
                    'livros_desc'      => $query->orderBy('books_count', 'desc'),
                    default            => $query->latest(),
                };
            }, function($query){
                $query->latest();
            })
            ->paginate(15, ['*'], 'pag')
            ->withQueryString();

        $filters = $request->only(['search', 'sort']);

        if (!in_array($filters['sort'] ?? null, ['nome_az', 'nome_za', 'livros_asc', 'livros_desc'], true)) {
            $filters['sort'] = '';
        }

        return Inertia::render('Authors/Index', [
            'authors' => $authors,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Authors/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'photo_path' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('photo_path')) {
            $path = $request->file('photo_path')->store('autores', 'public');
        }

        Author::create([
            'name' => $validated['name'],
            'photo_path' => $path ? '/media/' . $path : null,
        ]);

        return redirect()->route('autores.index')->with('success', 'Autor adicionado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $autore)
    {
        return Inertia::render('Authors/Show', [
            'author' => $autore,
            'books' => $autore->books()->with('publisher')->get()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Author $autore)
    {
       return Inertia::render('Authors/Edit', [
       'author' => $autore,
       ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Author $autore)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'photo_path' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        $finalPath = $autore->photo_path;

        // 1. Apagar a imagem antiga e guardar a nova imagem
        if ($request->hasFile('photo_path')) {
            if ($autore->photo_path && !str_contains($autore->photo_path, 'http')) {
                Storage::disk('public')->delete(str_replace('/media/', '', $autore->photo_path));
            }

            $path = $request->file('photo_path')->store('autores', 'public');
            $finalPath = '/media/' . $path;
        }

        $autore->update([
            'name' => $validated['name'],
            'photo_path' => $finalPath,
        ]);

        return redirect()->route('autores.show', $autore->id)->with('success', 'Autor atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $autore)
    {
        if ($autore->photo_path && !str_contains($autore->photo_path, 'http')) {
            $path = str_replace('/media/', '', $autore->photo_path);
            Storage::disk('public')->delete($path);
        }

        $autore->delete();
        return redirect()->route('autores.index')->with('success', 'Autor removido com sucesso!');
    }

    public function export(Request $request){
        try {
            return Excel::download(new AuthorsExport($request), 'autores.xlsx');
        } catch (Exception|\PhpOffice\PhpSpreadsheet\Exception $e) {
            return response()->json(['error' => 'Ocorreu um erro ao exportar os autores.'], 500);
        }
    }
}

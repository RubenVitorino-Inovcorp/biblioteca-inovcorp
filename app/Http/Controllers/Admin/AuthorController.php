<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Exports\AuthorsExport;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

class AuthorController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Author::class, 'author');
    }

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
            'photo_path' => $path ? '/storage/' . $path : null,
        ]);

        return redirect()->route('autores.index')->with('success', 'Autor adicionado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        return Inertia::render('Authors/Show', [
            'author' => $author,
            'books' => $author->books()->with('publisher')->get()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Author $author)
    {
       return Inertia::render('Authors/Edit', [
       'author' => $author,
       ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Author $author)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'photo_path' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        $finalPath = $author->photo_path;

        // 1. Apagar a imagem antiga e guardar a nova imagem
        if ($request->hasFile('photo_path')) {
            if ($author->photo_path && str_starts_with($author->photo_path, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $author->photo_path));
            }

            $path = $request->file('photo_path')->store('autores', 'public');
            $finalPath = '/storage/' . $path;
        }

        $author->update([
            'name' => $validated['name'],
            'photo_path' => $finalPath,
        ]);

        return redirect()->route('autores.show', $author->id)->with('success', 'Autor atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        $author->delete();
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


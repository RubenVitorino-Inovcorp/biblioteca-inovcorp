<?php

namespace App\Http\Controllers;
use App\Exports\BooksExport;
use App\Models\Author;
use App\Models\Publisher;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

use App\Models\Book;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $books = Book::query()
            ->with(['publisher', 'authors'])
            ->when($request->search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            // Filtro de Editora
            ->when($request->publisher, function ($query, $publisherId) {
                $query->where('publisher_id', $publisherId);
            })
            // Filtro de Autor
            ->when($request->author, function ($query, $authorId) {
                $query->whereHas('authors', function ($q) use ($authorId) {
                    $q->where('authors.id', $authorId);
                });
            })
            // Ordenação
            ->when($request->sort, function ($query, $sort) {
                match ($sort) {
                    'preco_asc'  => $query->orderBy('price', 'asc'),
                    'preco_desc' => $query->orderBy('price', 'desc'),
                    'titulo_az'  => $query->orderBy('title', 'asc'),
                    default      => $query->latest(),
                };
            }, function ($query) {
                $query->latest();
            })
            ->paginate(15, ['*'], 'pag')
            ->withQueryString();

        $filters = $request->only(['search', 'sort', 'publisher', 'author']);

        if (!in_array($filters['sort'] ?? null, ['preco_asc', 'preco_desc', 'titulo_az'], true)) {
            $filters['sort'] = '';
        }

        return Inertia::render('Books/Index', [
            'books' => $books,
            'filters' => $filters,
            'publishers' => Publisher::query()->orderBy('name')->get(),
            'authors' => Author::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Books/Create', [
            'publishers' => Publisher::query()->orderBy('name')->get(),
            'authors' => Author::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'bibliography' => 'nullable|string',
            'isbn' => 'nullable|string|unique:books,isbn',
            'price' => 'required|numeric|min:0',
            'publisher_id' => 'required|exists:publishers,id',
            'author_ids' => 'required|array',
            'author_ids.*' => 'exists:authors,id',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('imagens', 'public');
        }

        $book = Book::create([
            'title' => $validated['title'],
            'bibliography' => $validated['bibliography'] ?? null,
            'isbn' => $validated['isbn'] ?? null,
            'price' => $validated['price'],
            'publisher_id' => $validated['publisher_id'],
            'image_path' => $path ? '/media/' . $path : null,
        ]);

        $book->authors()->sync($validated['author_ids']);

        return redirect()->route('livros.index')->with('success', 'Livro adicionado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        return Inertia::render('Books/Show', [
            'book' => $book->load('authors', 'publisher'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        return Inertia::render('Books/Edit', [
            'book' => $book->load('authors', 'publisher'),
            'publishers' => Publisher::query()->orderBy('name')->get(),
            'authors' => Author::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'bibliography' => 'nullable|string',
            // Resolve o problema do ISBN: ignora o ID do livro atual na verificação de unicidade
            'isbn' => [
                'nullable',
                'string',
                Rule::unique('books')->ignore($book->id),
            ],
            'price' => 'required|numeric|min:0',
            'publisher_id' => 'required|exists:publishers,id',
            'author_ids' => 'required|array',
            'author_ids.*' => 'exists:authors,id',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        $finalPath = $book->image_path;

        // 1. Apagar a imagem antiga e guardar a nova imagem
        if ($request->hasFile('image_path')) {
            if ($book->image_path && !str_contains($book->image_path, 'http')) {
                Storage::disk('public')->delete(str_replace('/media/', '', $book->image_path));
            }

            $path = $request->file('image_path')->store('imagens', 'public');
            $finalPath = '/media/' . $path;
        }

        $book->update([
            'title'        => $validated['title'],
            'bibliography' => $request->bibliography,
            'isbn'         => $request->isbn,
            'price'        => $request->price,
            'publisher_id' => $request->publisher_id,
            'image_path'   => $finalPath,
        ]);

        $book->authors()->sync($request->author_ids);

        return redirect()->route('livros.show', $book->id)->with('success', 'Livro atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        if ($book->image_path && !str_contains($book->image_path, 'http')) {
            $path = str_replace('/media/', '', $book->image_path);
            Storage::disk('public')->delete($path);
        }

        $book->delete();
        return redirect()->route('livros.index')->with('success', 'Livro removido com sucesso!');
    }

    /**
     *  Export the books displayed by the user
     */

    public function export(Request $request){
        try {
            return Excel::download(new BooksExport($request), 'livros.xlsx');
        } catch (Exception|\PhpOffice\PhpSpreadsheet\Exception $e) {
            return response()->json(['error' => 'Ocorreu um erro ao exportar os livros.'], 500);
        }
    }
}

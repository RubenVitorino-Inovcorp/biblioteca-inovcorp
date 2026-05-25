<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BooksExport;
use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

class BookController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Book::class, 'book');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $books = Book::query()
            ->with(['publisher', 'authors'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
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
                    'preco_asc' => $query->orderBy('price', 'asc'),
                    'preco_desc' => $query->orderBy('price', 'desc'),
                    'titulo_az' => $query->orderBy('title', 'asc'),
                    'titulo_za' => $query->orderBy('title', 'desc'),
                    default => $query->latest(),
                };
            }, function ($query) {
                $query->latest();
            })
            ->paginate(15, ['*'], 'pag')
            ->withQueryString();

        $filters = $request->only(['search', 'sort', 'publisher', 'author']);

        if (! in_array($filters['sort'] ?? null, ['preco_asc', 'preco_desc', 'titulo_az', 'titulo_za'], true)) {
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
    public function create(Request $request): Response
    {
        $search = $request->query('search', '');
        $paginatedExternalBooks = null;

        if (trim($search) !== '') {

            $perPage = 3;
            $maxPages = 10;
            $page = (int) $request->query('page', 1);

            $startIndex = ($page - 1) * $perPage;

            $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
                'q' => $search,
                'maxResults' => $perPage,
                'startIndex' => $startIndex,
                'key' => config('services.google.books_key'),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                $googleTotal = $data['totalItems'] ?? 0;
                $totalItems = min($googleTotal, $perPage * $maxPages);
                $googlePublisher = null;
                $localPublisherId = null;

                $externalBooks = collect($data['items'] ?? [])->map(function ($item) {

                    $googlePublisher = $item['volumeInfo']['publisher'] ?? null;

                    $localPublisherId = $googlePublisher
                        ? Publisher::where('name', 'like', trim($googlePublisher))->first()?->id
                        : null;

                    return [
                        'google_id' => $item['id'] ?? null,
                        'titulo' => $item['volumeInfo']['title'] ?? 'Sem título',
                        'autores' => implode(', ', $item['volumeInfo']['authors'] ?? ['Autor Desconhecido']),
                        'publisher_id' => $localPublisherId,
                        'publisher_name' => $googlePublisher,
                        'capa' => $item['volumeInfo']['imageLinks']['thumbnail'] ?? null,
                        'isbn' => collect($item['volumeInfo']['industryIdentifiers'] ?? [])
                            ->firstWhere('type', 'ISBN_13')['identifier'] ?? null,
                        'description' => $item['volumeInfo']['description'] ?? '',
                    ];
                })->all();

                $paginatedExternalBooks = new LengthAwarePaginator(
                    $externalBooks,
                    $totalItems,
                    $perPage,
                    $page,
                    [
                        'path' => $request->url(),
                        'query' => $request->query(),
                    ]
                );
            }
        }

        return Inertia::render('Books/Create', [
            'publishers' => Publisher::select(['id', 'name'])->get(),
            'authors' => Author::select(['id', 'name'])->get(),
            'externalBooks' => $paginatedExternalBooks,
            'filters' => ['search' => $search],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'bibliography' => 'nullable|string',
            'isbn' => 'nullable|string|unique:books,isbn',
            'price' => 'required|numeric|min:0',
            'total_stock' => 'required|integer|min:0',
            'publisher_id' => 'required',
            'author_ids' => 'required|array',
            'author_ids.*' => 'required',
        ];

        if ($request->hasFile('image_path')) {
            $rules['image_path'] = 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048';
        } else {
            $rules['image_path'] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        $path = null;
        if ($request->hasFile('image_path')) {
            $path = '/storage/'.$request->file('image_path')->store('imagens', 'public');
        } elseif (is_string($request->image_path) && str_starts_with($request->image_path, 'http')) {
            $path = $request->image_path;
        }

        $publisherId = $validated['publisher_id'];
        if (!is_numeric($publisherId) || !\App\Models\Publisher::find($publisherId)) {
            $publisher = \App\Models\Publisher::firstOrCreate(['name' => $publisherId]);
            $publisherId = $publisher->id;
        }

        $authorIds = [];
        foreach ($validated['author_ids'] as $authorInput) {
            if (is_numeric($authorInput) && \App\Models\Author::find($authorInput)) {
                $authorIds[] = $authorInput;
            } else {
                $author = \App\Models\Author::firstOrCreate(['name' => $authorInput]);
                $authorIds[] = $author->id;
            }
        }

        $book = Book::create([
            'title' => $validated['title'],
            'bibliography' => $validated['bibliography'] ?? null,
            'isbn' => $validated['isbn'] ?? null,
            'price' => $validated['price'],
            'total_stock' => $validated['total_stock'],
            'available_stock' => $validated['total_stock'],
            'publisher_id' => $publisherId,
            'image_path' => $path,
        ]);

        $book->authors()->sync($authorIds);

        return redirect()->route('livros.index')->with('success', 'Livro adicionado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        return Inertia::render('Books/Show', [
            'book' => $book->load('authors', 'publisher'),
            'loans' => $book->loans()->with('user')->latest()->get(),
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
            'isbn' => [
                'nullable',
                'string',
                Rule::unique('books')->ignore($book->id),
            ],
            'price' => 'required|numeric|min:0',
            'total_stock' => 'required|integer|min:0',
            'publisher_id' => 'required',
            'author_ids' => 'required|array',
            'author_ids.*' => 'required',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        $finalPath = $book->image_path;

        // Apagar a imagem antiga e guardar a nova imagem
        if ($request->hasFile('image_path')) {
            if ($book->image_path && ! str_contains($book->image_path, 'http')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $book->image_path));
            }

            $path = $request->file('image_path')->store('imagens', 'public');
            $finalPath = '/storage/'.$path;
        }

        $diff = $request->total_stock - $book->total_stock;
        $newAvailableStock = $book->available_stock + $diff;

        if ($newAvailableStock < 0) {
            throw ValidationException::withMessages([
                'total_stock' => 'Não pode reduzir o stock total abaixo do número de livros atualmente requisitados.',
            ]);
        }

        $publisherId = $validated['publisher_id'];
        if (!is_numeric($publisherId) || !\App\Models\Publisher::find($publisherId)) {
            $publisher = \App\Models\Publisher::firstOrCreate(['name' => $publisherId]);
            $publisherId = $publisher->id;
        }

        $authorIds = [];
        foreach ($validated['author_ids'] as $authorInput) {
            if (is_numeric($authorInput) && \App\Models\Author::find($authorInput)) {
                $authorIds[] = $authorInput;
            } else {
                $author = \App\Models\Author::firstOrCreate(['name' => $authorInput]);
                $authorIds[] = $author->id;
            }
        }

        $book->update([
            'title' => $validated['title'],
            'bibliography' => $validated['bibliography'],
            'isbn' => $validated['isbn'],
            'price' => $validated['price'],
            'total_stock' => $validated['total_stock'],
            'available_stock' => $newAvailableStock,
            'publisher_id' => $publisherId,
            'image_path' => $finalPath,
        ]);

        $book->authors()->sync($authorIds);

        return redirect()->route('livros.show', $book->id)->with('success', 'Livro atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        if ($book->image_path && ! str_contains($book->image_path, 'http')) {
            $path = str_replace('/storage/', '', $book->image_path);
            Storage::disk('public')->delete($path);
        }

        $book->delete();

        return redirect()->route('livros.index')->with('success', 'Livro removido com sucesso!');
    }

    /**
     *  Export the books displayed by the user
     */
    public function export(Request $request)
    {
        try {
            return Excel::download(new BooksExport($request), 'livros.xlsx');
        } catch (Exception|\PhpOffice\PhpSpreadsheet\Exception $e) {
            return response()->json(['error' => 'Ocorreu um erro ao exportar os livros.'], 500);
        }
    }
}


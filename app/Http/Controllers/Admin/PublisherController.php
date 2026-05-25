<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Exports\PublishersExport;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

class PublisherController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Publisher::class, 'publisher');
    }

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
                match ($sort) {
                    'nome_az'          => $query->orderBy('name', 'asc'),
                    'nome_za'          => $query->orderBy('name', 'desc'),
                    'livros_asc'       => $query->orderBy('books_count', 'asc'),
                    'livros_desc'      => $query->orderBy('books_count', 'desc'),
                    default            => $query->latest(),
                };
            }, function ($query) {
            $query->latest();})
            ->paginate(15, ['*'], 'pag')
            ->withQueryString();

        $filters = $request->only(['search', 'sort']);

        if (!in_array($filters['sort'] ?? null, ['nome_az', 'nome_za', 'livros_asc', 'livros_desc'], true)) {
            $filters['sort'] = '';
        }

        return Inertia::render('Publishers/Index', [
            'publishers' => $publishers,
            'filters' => $filters,
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
            'logo_path' => $path ? '/storage/' . $path : null,
        ]);

        return redirect()->route('editoras.index')->with('success', 'Editora adicionada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Publisher $publisher)
    {
        return Inertia::render('Publishers/Show', [
            'publisher' => $publisher,
            'books' => $publisher->books()->with('publisher')->get()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Publisher $publisher)
    {
        return Inertia::render('Publishers/Edit', [
            'publisher' => $publisher,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Publisher $publisher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo_path' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        $finalPath = $publisher->logo_path;

        // 1. Apagar a imagem antiga e guardar a nova imagem
        if ($request->hasFile('logo_path')) {
            if ($publisher->logo_path && !str_starts_with($publisher->logo_path, 'http')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $publisher->logo_path));
            }

            $path = $request->file('logo_path')->store('editoras', 'public');
            $finalPath = '/storage/' . $path;
        }

        $publisher->update([
            'name' => $validated['name'],
            'logo_path' => $finalPath,
        ]);

        return redirect()->route('editoras.show', $publisher->id)->with('success', 'Editora atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publisher $publisher)
    {
        if ($publisher->logo_path && !str_starts_with($publisher->logo_path, 'http')) {
            $path = str_replace('/storage/', '', $publisher->logo_path);
            Storage::disk('public')->delete($path);
        }

        $publisher->delete();
        return redirect()->route('editoras.index')->with('success', 'Editora removida com sucesso!');
    }

    public function export(Request $request){
        try {
            return Excel::download(new PublishersExport($request), 'editoras.xlsx');
        } catch (Exception|\PhpOffice\PhpSpreadsheet\Exception $e) {
            return response()->json(['error' => 'Ocorreu um erro ao exportar as editoras.'], 500);
        }
    }
}


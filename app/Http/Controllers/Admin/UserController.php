<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(Request $request)
    {
        return Inertia::render('Users/Index', [
            'users' => User::query()
                ->when($request->search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->paginate(15, ['*'], 'pag')
                ->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:admin,user',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('utilizadores.index')->with('success', 'Utilizador adicionado com sucesso!');
    }


    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required|string|in:admin,user',
        ]);

        $user->update($request->only('name', 'email', 'role'));

        return back()->with('success', 'Utilizador atualizado.');
    }

    public function show(User $user)
    {
        return Inertia::render('Users/Show', [
            'user' => $user,
            'loans' => $user->loans()
                ->with('book')
                ->latest()
                ->get(),
        ]);
    }

    // public function export(Request $request){
    //     try {
    //         return Excel::download(new UsersExport($request), 'utilizadores.xlsx');
    //     } catch (Exception|\PhpOffice\PhpSpreadsheet\Exception $e) {
    //         return response()->json(['error' => 'Ocorreu um erro ao exportar os utilizadores.'], 500);
    //     }
    // }
}

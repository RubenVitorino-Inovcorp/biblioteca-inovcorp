<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\PublisherController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('livros/exportar', [BookController::class, 'export'])->name('livros.export');
    Route::get('autores/exportar', [AuthorController::class, 'export'])->name('autores.export');
    Route::get('editoras/exportar', [PublisherController::class, 'export'])->name('editoras.export');

    Route::resource('/livros', BookController::class)
    ->parameter('livros', 'book');
    Route::resource('/autores', AuthorController::class);
    Route::resource('/editoras', PublisherController::class);

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

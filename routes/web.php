<?php

use App\Http\Controllers\Admin\AuthorController as AdminAuthorController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\PublisherController as AdminPublisherController;
use App\Http\Controllers\Admin\LoanController as AdminLoanController;
use App\Http\Controllers\User\AuthorController as UserAuthorController;
use App\Http\Controllers\User\BookController as UserBookController;
use App\Http\Controllers\User\PublisherController as UserPublisherController;
use App\Http\Controllers\User\LoanController as UserLoanController;
use App\Http\Middleware\IsAdmin;
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

    // Role: Admin
    Route::middleware([IsAdmin::class])->group(function () {
        Route::get('admin/livros/exportar', [AdminBookController::class, 'export'])->name('livros.export');
        Route::get('admin/autores/exportar', [AdminAuthorController::class, 'export'])->name('autores.export');
        Route::get('admin/editoras/exportar', [AdminPublisherController::class, 'export'])->name('editoras.export');

        Route::resource('admin/livros', AdminBookController::class)->parameter('livros', 'book');
        Route::resource('admin/autores', AdminAuthorController::class)->parameter('autores', 'author');
        Route::resource('admin/editoras', AdminPublisherController::class)->parameter('editoras', 'publisher');
        Route::resource('admin/utilizadores', AdminUserController::class)->parameter('utilizadores', 'user');
        Route::resource('admin/requisicoes', AdminLoanController::class)->parameter('requisicoes', 'loan');
        Route::post('admin/requisicoes/{loan}/aprovar', [AdminLoanController::class, 'approve'])->name('requisicoes.approve');
        Route::post('admin/requisicoes/{loan}/rejeitar', [AdminLoanController::class, 'reject'])->name('requisicoes.reject');
        Route::post('admin/requisicoes/{loan}/devolver', [AdminLoanController::class, 'returnBook'])->name('requisicoes.devolver');
    });

    // Role: Usuário Regular
    Route::resource('/livros', UserBookController::class)->only(['index', 'show'])->parameter('livros', 'book')->names('catalog.livros');
    Route::resource('/autores', UserAuthorController::class)->only(['index', 'show'])->parameter('autores', 'author')->names('catalog.autores');
    Route::resource('/editoras', UserPublisherController::class)->only(['index', 'show'])->parameter('editoras', 'publisher')->names('catalog.editoras');
    Route::resource('/requisicoes', UserLoanController::class)->only(['index', 'show', 'store'])->parameter('requisicoes', 'loan')->names('catalog.requisicoes');
    Route::post('/requisicoes/{loan}/devolver', [UserLoanController::class, 'returnBook'])->name('catalog.requisicoes.devolver');

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

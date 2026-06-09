<?php

use App\Http\Controllers\Admin\AuthorController as AdminAuthorController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\GoogleBookController;
use App\Http\Controllers\Admin\LoanController as AdminLoanController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PublisherController as AdminPublisherController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\User\AuthorController as UserAuthorController;
use App\Http\Controllers\User\BookAlertController;
use App\Http\Controllers\User\BookController as UserBookController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\LoanController as UserLoanController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\PublisherController as UserPublisherController;
use App\Http\Controllers\User\ReviewController as UserReviewController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Dashboard');
})->name('home');

Route::get('/dashboard', function () {
    return redirect('/');
})->name('dashboard');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Role: Admin
    Route::middleware([IsAdmin::class])->group(function () {
        Route::get('admin/livros/exportar', [AdminBookController::class, 'export'])->name('livros.export');
        Route::get('admin/livros/procurar-google', [GoogleBookController::class, 'index'])->name('livros.google-index');
        Route::get('admin/livros/procurar-google/exportar', [GoogleBookController::class, 'export'])->name('livros.google-export');
        Route::get('admin/livros/procurar-google/{id}', [GoogleBookController::class, 'show'])->name('livros.google-show');
        Route::get('admin/autores/exportar', [AdminAuthorController::class, 'export'])->name('autores.export');
        Route::get('admin/editoras/exportar', [AdminPublisherController::class, 'export'])->name('editoras.export');

        Route::resource('admin/livros', AdminBookController::class)->parameter('livros', 'book');
        Route::resource('admin/autores', AdminAuthorController::class)->parameter('autores', 'author');
        Route::resource('admin/editoras', AdminPublisherController::class)->parameter('editoras', 'publisher');
        Route::resource('admin/utilizadores', AdminUserController::class)->parameter('utilizadores', 'user');
        Route::resource('admin/requisicoes', AdminLoanController::class)->parameter('requisicoes', 'loan');
        Route::resource('admin/encomendas', AdminOrderController::class)->parameter('encomendas', 'order')->names('admin.encomendas');
        Route::resource('admin/opinioes', AdminReviewController::class)->except(['create', 'store', 'edit', 'update'])->names('admin.opinioes')->parameter('opinioes', 'review');

        Route::post('admin/opinioes/{review}/aprovar', [AdminReviewController::class, 'approve'])->name('opinioes.aprovar');
        Route::post('admin/opinioes/{review}/rejeitar', [AdminReviewController::class, 'reject'])->name('opinioes.rejeitar');

        Route::post('admin/requisicoes/{loan}/aprovar', [AdminLoanController::class, 'approve'])->name('requisicoes.approve');
        Route::post('admin/requisicoes/{loan}/rejeitar', [AdminLoanController::class, 'reject'])->name('requisicoes.reject');
        Route::post('admin/requisicoes/{loan}/devolver', [AdminLoanController::class, 'returnBook'])->name('requisicoes.devolver');
    });

    // Role: Usuário Regular
    Route::resource('/livros', UserBookController::class)->only(['index', 'show'])->parameter('livros', 'book')->names('catalog.livros');
    Route::resource('/autores', UserAuthorController::class)->only(['index', 'show'])->parameter('autores', 'author')->names('catalog.autores');
    Route::resource('/editoras', UserPublisherController::class)->only(['index', 'show'])->parameter('editoras', 'publisher')->names('catalog.editoras');
    Route::resource('/requisicoes', UserLoanController::class)->only(['index', 'show', 'store'])->parameter('requisicoes', 'loan')->names('catalog.requisicoes');
    Route::resource('/opinioes', UserReviewController::class)->parameter('opinioes', 'review')->names('opinioes');
    Route::resource('/encomendas', OrderController::class)->only(['index', 'show'])->parameter('encomendas', 'order')->names('encomendas');

    Route::post('/requisicoes/{loan}/devolver', [UserLoanController::class, 'returnBook'])->name('catalog.requisicoes.devolver');

    Route::post('/livros/{book}/alerta', [BookAlertController::class, 'store'])->name('catalog.livros.alerta.store');
    Route::delete('/livros/{book}/alerta', [BookAlertController::class, 'destroy'])->name('catalog.livros.alerta.destroy');

    Route::get('/carrinho', [CartController::class, 'index'])->name('catalog.carrinho.index');
    Route::post('/carrinho', [CartController::class, 'store'])->name('catalog.carrinho.store');
    Route::delete('/carrinho', [CartController::class, 'destroy'])->name('catalog.carrinho.destroy');

    Route::post('/encomendas', [OrderController::class, 'store'])->name('catalog.encomendas.store');

    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('catalog.checkout.success');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('catalog.checkout.cancel');

    Route::get('/checkout/{order}', CheckoutController::class)->name('catalog.checkout.process');

});

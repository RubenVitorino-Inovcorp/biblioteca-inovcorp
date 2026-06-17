<?php

namespace App\Providers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Publisher;
use App\Observers\AuthorObserver;
use App\Observers\BookObserver;
use App\Observers\LoanObserver;
use App\Observers\PublisherObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        Model::shouldBeStrict();
        Model::automaticallyEagerLoadRelationships();
        Loan::observe(LoanObserver::class);
        Author::observe(AuthorObserver::class);
        Book::observe(BookObserver::class);
        Publisher::observe(PublisherObserver::class);
        Route::resourceVerbs([
            'create' => 'criar',
            'edit' => 'editar',
            'page' => 'pagina',
        ]);
    }
}

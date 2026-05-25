<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\AuthorObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy(AuthorObserver::class)]
class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'photo_path',
    ];

    /**
     * Resolve o URL público da foto ou retorna a imagem por defeito.
     */
    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes) => $attributes['photo_path'] 
                ? asset($attributes['photo_path']) 
                : asset('/storage/autores/default.webp') 
        );
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }
}

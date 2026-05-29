<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\BookObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

#[ObservedBy(BookObserver::class)]
class Book extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'title',
        'bibliography',
        'isbn',
        'price',
        'total_stock',
        'available_stock',
        'publisher_id',
        'image_path',
    ];

    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'title' => (string) $this->title,
            'bibliography' => (string) $this->bibliography,
            'isbn' => (string) $this->isbn,
            'publisher' => (string) $this->publisher->name,
            'authors' => (string) $this->authors->pluck('name')->join(', '),
        ];
    }

    protected $appends = ['is_available'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'bibliography' => 'encrypted',
            'price' => 'decimal:2',
            'published_at' => 'date:Y-m-d',
        ];
    }

    /**
     * Resolve o URL público da imagem ou retorna null.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes) => $attributes['image_path']
                ? (str_starts_with($attributes['image_path'], 'http') ? $attributes['image_path'] : asset($attributes['image_path']))
                : asset('/storage/imagens/default.webp')
        );
    }

    /**
     * Determina se o livro está disponível.
     */
    protected function isAvailable(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => (int) ($attributes['available_stock'] ?? 0) > 0,
        );
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}

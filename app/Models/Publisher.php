<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\PublisherObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(PublisherObserver::class)]
class Publisher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo_path',
    ];

    protected $appends = ['logo_url'];

    /**
     * Resolve o URL público do logo ou retorna a imagem por defeito.
     */
    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes) => ($attributes['logo_path'] ?? null)
                ? (str_starts_with($attributes['logo_path'], 'http') ? $attributes['logo_path'] : asset($attributes['logo_path']))
                : asset('/storage/editoras/default.webp')
        );
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'bibliography',
        'isbn',
        'price',
        'total_stock',
        'available_stock',
        'publisher_id',
    ];

    public function authors(): BelongsToMany {
        return $this->belongsToMany(Author::class);
    }

    public function publisher(): BelongsTo {
        return $this->belongsTo(Publisher::class);
    }

    public function loans(): hasMany {
        return $this->hasMany(Loan::class);
    }

    protected function isAvailable(): Attribute {
        return Attribute::make(
            get: fn () => $this->available_stock > 0,
        );
    }

    protected $appends = ['is_available'];

    protected function casts(): array {
        return [
            'bibliography' => 'encrypted',
            'price' => 'decimal:2',
            'published_at' => 'date:Y-m-d',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    use HasFactory;
    public function authors(): BelongsToMany {
        return $this->belongsToMany(Author::class);
    }

    public function publisher(): BelongsTo {
        return $this->belongsTo(Publisher::class);
    }

    protected function casts(): array {
        return [
            'price' => 'decimal:2',
            'published_at' => 'date:Y-m-d',
            'is_available' => 'boolean',
        ];
    }
}

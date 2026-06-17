<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrderStatus;
use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

#[ObservedBy(OrderObserver::class)]
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'total_price',
        'delivery_address',
        'status',
        'stripe_session_id',
        'user_id',
    ];

    public function casts(): array
    {
        return [
            'total_price' => 'float',
            'status' => OrderStatus::class,
        ];
    }

    protected $appends = [
        'status_label',
        'status_color',
    ];

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reduceItemsStock(): void
    {
        DB::transaction(function () {
            foreach ($this->items as $item) {
                $book = Book::where('id', $item->book_id)->lockForUpdate()->first();
                if (! $book || $book->available_stock < $item->quantity) {
                    throw new \RuntimeException("Stock insuficiente para o livro {$item->book_id}");
                }
                $book->decrement('available_stock', $item->quantity);
            }
        });
    }

    public function restoreItemsStock(): void
    {
        DB::transaction(function () {
            foreach ($this->items as $item) {
                $item->book->increment('available_stock', $item->quantity);
            }
        });
    }
}

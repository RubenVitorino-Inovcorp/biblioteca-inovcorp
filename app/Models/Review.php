<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ReviewStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'loan_id',
        'review_title',
        'review_text',
        'rating',
        'rejection_reason',
        'status',
    ];

    protected $appends = [
        'status_label',
        'status_color',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:d/m/Y',
            'updated_at' => 'datetime:d/m/Y',
            'status' => ReviewStatus::class,
        ];
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $this->status?->label(),
        );
    }

    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $this->status?->color(),
        );
    }

    protected static function boot(): void
    {
        parent::boot();

        // static::creating(function (Loan $loan): void {
        //     $maxAttempts = 5;
        //     for ($i = 0; $i < $maxAttempts; $i++) {
        //         /** @var Loan|null $lastloan */
        //         $lastloan = self::lockForUpdate()->latest('id')->first();
        //         $number = $lastloan ? (int) str_replace('REQ-', '', (string) $lastloan->loan_number) + 1 : 1;
        //         $loan->loan_number = 'REQ-'.str_pad((string) $number, 6, '0', STR_PAD_LEFT);

        //         if (! self::where('loan_number', $loan->loan_number)->exists()) {
        //             break;
        //         }
        //     }
        // });
    }
}

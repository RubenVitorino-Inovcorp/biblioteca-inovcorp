<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LoanStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'user_photo_snapshot',
        'start_date',
        'estimated_return_date',
        'end_date',
        'status',
    ];

    protected $appends = [
        'status_label',
        'status_color',
        'elapsed_days',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date:d/m/Y H:i',
            'estimated_return_date' => 'date:d/m/Y',
            'end_date' => 'date:d/m/Y H:i',
            'status' => LoanStatus::class,
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

    protected function elapsedDays(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): int {
                if (empty($attributes['start_date'])) {
                    return 0;
                }

                $startDate = Carbon::parse($attributes['start_date']);
                $endDate = ! empty($attributes['end_date']) ? Carbon::parse($attributes['end_date']) : now();

                return (int) $startDate->diffInDays($endDate);
            },
        );
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Loan $loan): void {
            $maxAttempts = 5;
            for ($i = 0; $i < $maxAttempts; $i++) {
                /** @var Loan|null $lastloan */
                $lastloan = self::lockForUpdate()->latest('id')->first();
                $number = $lastloan ? (int) str_replace('REQ-', '', (string) $lastloan->loan_number) + 1 : 1;
                $loan->loan_number = 'REQ-'.str_pad((string) $number, 6, '0', STR_PAD_LEFT);

                if (! self::where('loan_number', $loan->loan_number)->exists()) {
                    break;
                }
            }
        });
    }
}

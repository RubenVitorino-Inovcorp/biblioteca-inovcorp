<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\LoanStatus;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the URL to the user's profile photo.
     * Handles three storage formats:
     */
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): ?string {
                if (empty($attributes['profile_photo_path'])) {
                    return asset('/storage/fotos-perfil/default.webp');
                }

                // URLs externas (dicebear, gravatar, etc.)
                if (str_starts_with($attributes['profile_photo_path'], 'http')) {
                    return $attributes['profile_photo_path'];
                }

                // Já tem o prefixo /storage/
                if (str_starts_with($attributes['profile_photo_path'], '/storage/')) {
                    return $attributes['profile_photo_path'];
                }

                // Caminho antigo do Jetstream sem prefixo (ex: profile-photos/xxx.png)
                return '/storage/'.$attributes['profile_photo_path'];
            }
        );
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function canMakeLoans(): bool
    {
        return $this->loans()->whereIn('status', [LoanStatus::ACTIVE, LoanStatus::OVERDUE, LoanStatus::PENDING, LoanStatus::RETURN_PENDING])->count() < 3;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }
}

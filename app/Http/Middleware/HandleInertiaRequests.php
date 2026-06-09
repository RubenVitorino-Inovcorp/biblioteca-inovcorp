<?php

namespace App\Http\Middleware;

use App\Enums\CartStatus;
use App\Enums\UserRole;
use App\Models\Cart;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'roles' => [
                'ADMIN' => UserRole::ADMIN,
                'USER' => UserRole::USER,
            ],
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'profile_photo_url' => $request->user()->profile_photo_url,
                    'profile_photo_path' => $request->user()->profile_photo_path,
                    'is_admin' => $request->user()->isAdmin(),
                    'role' => [
                        'id' => $request->user()->role?->value,
                        'name' => $request->user()->role?->label(),
                    ],
                ] : null,
            ],
            'cart' => fn () => $request->user()
                ? Cart::where('user_id', $request->user()->id)
                    ->where('status', CartStatus::ACTIVE->value)
                    ->with('items.book')
                    ->first()
                : null,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}

<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Cart;
use App\Models\User;

class CartPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === UserRole::ADMIN) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // View any applies to the index action which shows only the user's own cart, so true
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Cart $cart): bool
    {
        return $user->id === $cart->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Cart $cart): bool
    {
        return $user->id === $cart->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Cart $cart): bool
    {
        return $user->id === $cart->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Cart $cart): bool
    {
        return $user->id === $cart->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Cart $cart): bool
    {
        return $user->id === $cart->user_id;
    }
}

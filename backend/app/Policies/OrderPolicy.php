<?php

namespace App\Policies;

use App\Constants\Role;
use App\Exceptions\RoleExceptions;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     * @throws RoleExceptions
     */
    public function viewAny(User $user): bool | RoleExceptions
    {
        return $user->role === Role::ADMIN
                ? true
                : throw RoleExceptions::unAuthorized();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->id == $order->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        return $user->id == $order->user_id || $user->role == Role::ADMIN;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->id == $order->user_id || $user->role == Role::ADMIN;
    }
}
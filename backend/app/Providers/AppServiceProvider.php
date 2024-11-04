<?php

namespace App\Providers;

use App\Constants\OrderStatus;
use App\Constants\Role;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define gates for each order status
        Gate::define(OrderStatus::ACCEPTED, fn(User $user, Order $order) => $this->updateStatus($user, $order, OrderStatus::ACCEPTED));
        Gate::define(OrderStatus::DECLINED, fn(User $user, Order $order) => $this->updateStatus($user, $order, OrderStatus::DECLINED));
        Gate::define(OrderStatus::CANCELED, fn(User $user, Order $order) => $this->updateStatus($user, $order, OrderStatus::CANCELED));
        Gate::define(OrderStatus::PENDING, fn(User $user, Order $order) => $this->updateStatus($user, $order, OrderStatus::PENDING));
    }

    protected function updateStatus(User $user, Order $order, string $status): bool
    {
        $adminStatuses = [OrderStatus::ACCEPTED, OrderStatus::DECLINED];
        $ownerStatuses = [OrderStatus::CANCELED, OrderStatus::PENDING];

        if (in_array($status, $adminStatuses)) {
            return $user->role === Role::ADMIN;
        }

        if (in_array($status, $ownerStatuses)) {
            return $user->id === $order->user_id;
        }

        return false;
    }
}

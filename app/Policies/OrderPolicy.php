<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /** Super admin bypass. */
    public function before(User $user): ?bool
    {
        return $user->division === 'super_admin' ? true : null;
    }

    public function view(User $user, Order $order): bool
    {
        return $this->ownerOrStaff($user, $order);
    }

    public function update(User $user, Order $order): bool
    {
        return $this->ownerOrStaff($user, $order);
    }

    /** Pemilik order, atau siapa pun yang bukan customer (= staf). */
    protected function ownerOrStaff(User $user, Order $order): bool
    {
        return $order->user_id === $user->id || ! $user->isCustomer();
    }
}

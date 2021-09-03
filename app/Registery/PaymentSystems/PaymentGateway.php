<?php

namespace App\Registery\PaymentSystems;

use App\Models\User;
use App\Models\Order;

interface PaymentGateway {
    public function pay (User $user, Order $order);
}

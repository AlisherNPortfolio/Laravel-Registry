<?php

namespace App\Registery\PaymentSystems\Click;

use App\Models\User;
use App\Models\Order;
use App\Registery\PaymentSystems\PaymentGateway;
use Illuminate\Support\Facades\Redirect;

class ClickPaymentGateway implements PaymentGateway {
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $this->apiKey;
    }

    public function pay(User $user, Order $order)
    {
        // Click to'lov tizimining biznes-logikasi
        return new Redirect('/payment/click');
    }
}

<?php

namespace App\Registery\PaymentSystems\Payme;

use App\Models\User;
use App\Models\Order;
use App\Registery\PaymentSystems\PaymentGateway;
use Illuminate\Support\Facades\Redirect;

class PaymePaymentGateway implements PaymentGateway {
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $this->apiKey;
    }

    public function pay(User $user, Order $order)
    {
        // Payme to'lov tizimining biznes-logikasi
        return new Redirect('/payment/payme');
    }
}

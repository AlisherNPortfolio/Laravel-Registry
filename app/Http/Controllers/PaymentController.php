<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Registry\PaymentGatewayRegistry;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{

    private $gatewayRegistry;

    public function __construct(PaymentGatewayRegistry $registry)
    {
        $this->gatewayRegistry = $registry;
    }

    public function pay(Request $request, Order $order)
    {
        return $this->gatewayRegistry->get($request->get('gateway'))
                ->pay(Auth::user(), $order);
    }
}

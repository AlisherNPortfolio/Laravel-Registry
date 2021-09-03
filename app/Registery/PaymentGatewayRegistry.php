<?php

namespace App\Registry;

use App\Registery\PaymentSystems\PaymentGateway;
use Exception;

class PaymentGatewayRegistry {

    protected $gateways = [];

    public function register($name, PaymentGateway $instance)
    {
        $this->gateways[$name] = $instance;

        return $this;
    }

    public function get($name )
    {
        if (in_array($name, $this->gateways)) {
            return $this->gateways[$name];
        } else {
            throw new Exception("Invalid gateway: $name");
        }
    }
}

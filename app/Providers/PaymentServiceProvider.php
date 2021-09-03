<?php

namespace App\Providers;

use App\Registery\PaymentSystems\Click\ClickPaymentGateway;
use App\Registery\PaymentSystems\Payme\PaymePaymentGateway;
use App\Registry\PaymentGatewayRegistry;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // PaymentGatewayRegistry obyektini butun dastur bo'ylab yagona bo'lishi ta'minlanadi
        $this->app->singleton(PaymentGatewayRegistry::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // To'lov tizimlari registery obyektiga berib chiqiladi

        $this->app->make(PaymentGatewayRegistry::class)
        ->register('click', new ClickPaymentGateway(Config::get('payment.click.api_key')));

        $this->app->make(PaymentGatewayRegistry::class)
        ->register('payme', new PaymePaymentGateway(Config::get('payment.payme.api_key')));
    }
}

# Laravelda registery pattern-ning ishlatilishi

Aslida, quyida ko'riladigan registry klasi maxsus pattern emas.

Faraz qilaylik, tizimda ko'plab to'lov tizimlari ishlatilgan bo'lib, foydalanuvchilar faqat o'zlariga kerak bo'lgan to'lov tizimini tanlashlari kerak. Yoki, tizim holatiga qarab kerakli to'lov tizimlari ishlatilsin xolos.
Har qanday holatda ham, kodimiz "toza" ko'rinishda qolishi kerak.

### Quyidagi misolda ko'ramiz:

Bizning tizimimizda Click va Payme to'lov tizimlari integratsiya qilingan bo'lib, foydalanuvchi o'ziga kerakli tizimni tanlagan holda to'lovni amalga oshirishi kerak. Bunda, integratsiya qilingan har qaysi to'lov tizimi o'zining alohida biznes-logikasiga, sahifasiga va API-lariga ega bo'ladi.

### Bu muammoni hal qilish uchun, asosiy gateway klas, har bir to'lov tizimi uchun gateway/payment provider va ularga mos interfeyslarni yozish kerak bo'ladi.

```
    interface PaymentGateway {
        public function pay (User $user, Order $order);
    }

    class ClickPaymentGateway implements PaymentGateway {

        // konstruktorda umumiy ma'lumotlar yuklanadi
        function __construct ($apiKey) {
            $this->apiKey = $apiKey;
        }

        function pay (User $payee, Order $order) {
            // Click to'lov tizimining biznes-logikasi
            return new Redirect("/payment/click");
        }

    }

    class PaymePaymentGateway implements PaymentGateway {

        // konstruktorda umumiy ma'lumotlar yuklanadi
        function __construct ($apiKey) {
            $this->apiKey = $apiKey;
        }

        function pay (User $user, Order $order) {
            // Payme to'lov tizimining biznes-logikasi
            return new Redirect("/payment/payme");
        }

    }
```

Endi, to'lov tizimlarini dinamik holda chaqirish kodini ko'ramiz. Buning uchun registry klasi yoziladi:

```
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
```
Register klasni ochganimizdan keyin, unga to'lov tizimi obyektlarini berishimiz kerak. Bu ishni bajarishga eng qulay joy esa, albatta, service provider hisoblanadi:
1. Avval, service providerni yaratib olamiz: `php artisan make:provider PaymentServiceProvider`
2. Keyin, provider ichida payment gatewaylarni register klasiga berib chiqamiz:
```
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
```
3. Service provider config/app.php faylida ro'yxatdan o'tkaziladi:
```
    //...
    'providers' => [

            // ...

            /*
            * Package Service Providers...
            */
            App\Providers\PaymentServiceProvider::class,

            //...

        ],
        //...
```
### Ishlatilishi

Endi, to'lov tizimlarini controller ichida ishlatamiz:
1. PaymentController konstrolleri ochiladi: `php artisan make:controller PaymentController`
2. PaymentController.php:
```
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
```

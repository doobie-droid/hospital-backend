<?php

namespace App\Services\Payment\Providers\Paystack;


use  App\Services\Payment\Providers\Paystack\Main;
use Illuminate\Support\Facades\Log;

class Paystack
{
    protected $driver;

    public function __construct()
    {

        $this->driver = new Main;
    }

    public function makeMobilePayment(array $payload): \stdClass
    {
        return $this->driver->makeMobilePayment($payload);
    }

    public function makeUssdPayment(array $payload): \stdClass
    {
        return $this->driver->makeUssdPayment($payload);
    }
}

<?php

namespace App\Services\Payment\Providers\Flutterwave;


use App\Models\PaymentAccount;
use  App\Services\Payment\Providers\Flutterwave\Main;
use Illuminate\Support\Facades\Log;

class Flutterwave
{
    protected $driver;

    public function __construct()
    {

        $this->driver = new Main;
    }

    public function validateAccountNumber(string $account_number, string $bank_code): \stdClass
    {
        return $this->driver->validateAccountNumber($account_number, $bank_code);
    }

    public function makeCardPayment(string $encrypted_card_data): \stdClass
    {
        return $this->driver->makeCardPayment($encrypted_card_data);
    }

    public function verifyCardPayment(array $payload): \stdClass
    {
        return $this->driver->verifyCardPayment($payload);
    }
}

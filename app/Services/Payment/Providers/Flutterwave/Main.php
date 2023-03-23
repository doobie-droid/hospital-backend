<?php

namespace App\Services\Payment\Providers\Flutterwave;



use App\Services\API;
use Illuminate\Support\Facades\Log;

class Main extends API
{
    public function __construct()
    {
        $this->secret = config('payment.providers.flutterwave.secret_key');
    }

    public function baseUrl(): string
    {
        return 'https://api.flutterwave.com/';
    }



    public function validateAccountNumber(string $account_number, string $bank_code): \stdClass
    {
        $data = [
            'account_number' => $account_number,
            'account_bank' => $bank_code,
        ];
        return $this->_post('v3/accounts/resolve', $data);
    }
    public function makeCardPayment(string $encrypted_card_data): \stdClass
    {
        $data = [
            'client' => $encrypted_card_data,
        ];

        return $this->_post('v3/charges?type=card', $data);
    }

    public function verifyCardPayment(array $payload): \stdClass
    {

        return $this->_post('v3/validate-charge', $payload);
    }
}

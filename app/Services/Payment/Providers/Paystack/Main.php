<?php

namespace App\Services\Payment\Providers\Paystack;



use App\Services\API;

use GuzzleHttp\ClientException;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Illuminate\Support\Facades\Log;

class Main extends API
{
    public function __construct()
    {
        $this->secret = config('payment.providers.paystack.secret_key');
    }

    public function baseUrl(): string
    {
        return 'https://api.paystack.co/';
    }



    public function makeMobilePayment(array $payload): \stdClass
    {

        return $this->_post('charge', $payload);
    }

    public function makeUssdPayment(array $payload): \stdClass
    {
        return $this->_post('charge', $payload);
    }
}

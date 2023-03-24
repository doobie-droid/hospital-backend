<?php

namespace App\Jobs\Payments;

// use App\Jobs\Payment\Purchase as PurchaseJob;
// use App\Services\Payment\Providers\Flutterwave\Flutterwave as FlutterwavePayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Update implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $amount;
    public $clafiya_reference;
    public $payment_provider;
    public $payment_provider_reference;
    public $payment_time;
    public $currency;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->amount = $data['amount'];
        $this->clafiya_reference = $data['clafiya_reference'];
        $this->payment_provider = $data['payment_provider'];
        $this->payment_provider_reference = $data['payment_provider_reference'];
        $this->payment_time = $data['payment_time'];
        $this->currency = $data['currency'];
    }


    public function handle()
    {
        Log::info('Payment received successfully from the job');
        // $flutterwave = new Flutterwave;
        // $req = $flutterwave->verifyTransaction($request->data['id']);
        // if ($req->status !== 'success' || $req->data->status !== 'successful') {
        //     //update the payment table to show failed payment
        //     return $this->respondWithSuccess('Payment received successfully');
        // }
        //update the payment table to show successful payment
        //notify the user of successful payment



        // $flutterwave = new FlutterwavePayment;
        // $req = $flutterwave->verifyTransaction($this->provider_response['transaction_id']);
        // if (($req->status === 'success' && $req->data->status === 'successful')) {
        //     PurchaseJob::dispatch([
        //         'total_amount' => $req->data->amount,
        //         'total_fees' => $req->data->app_fee,
        //         'provider' => 'flutterwave',
        //         'provider_id' => $req->data->id,
        //         'user' => $this->user,
        //         'items' => $this->items,
        //     ]);
        // }
    }

    public function failed(\Throwable $exception)
    {
        Log::error($exception);
    }
}

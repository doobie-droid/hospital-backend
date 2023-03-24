<?php

namespace App\Jobs\Payments;

use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Services\Payment\Providers\Flutterwave\Flutterwave;
use Illuminate\Support\Facades\Mail;

class Update implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $amount;
    public $clafiya_reference;
    public $payment_provider;
    public $payment_provider_reference;
    public $currency;
    public $verification_id;
    public $payment_status;
    public $message;
    public $mail_template;
    public $user;
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
        $this->currency = $data['currency'];
        $this->verification_id = $data['verification_id'];
    }


    public function handle()
    {
        Log::info('Payment received successfully from the job');
        $flutterwave = new Flutterwave;
        $req = $flutterwave->verifyTransaction($this->verification_id);
        Log::info('here 1');
        if ($req->status !== 'success' || $req->data->status !== 'successful') {
            $this->payment_status = 'failed';
        } else {
            $this->payment_status = 'success';
        }
        Log::info('here 2');
        if ($req->data->currency !== 'NGN' || $this->currency !== 'NGN') {
            $this->message = 'Payment failed because your payment was not in naira';
            $this->payment_status = 'failed';
        }
        Log::info('here 3');
        $payment = Payment::where('clafiya_reference', $this->clafiya_reference)->first();
        Log::info($payment);
        if (!$payment) {
            $this->message = 'Payment failed because we could not find the payment you were trying to make';
            $this->payment_status = 'failed';
        }
        Log::info('here 4');
        Log::info('appointment id');
        Log::info($payment->appointment_id);
        $appointment = Appointment::where('id', $payment->appointment_id)->first();
        Log::info('here 4.1');
        if ($this->payment_status == 'success') {
            //do stuff
            $payment->status = 'success';
            Log::info('here 4.2');
            $appointment->status = 1;
            Log::info('here 4.3');
            $appointment->payment_id = $payment->id;
            Log::info('here 4.4');
            $appointment->save();
            Log::info('here 4.5');
            $this->mail_template = 'emails.payments.success';
            Log:
            info('here 4.6');
        } else {
            $payment->status = 'failed';
            $this->mail_template = 'emails.payments.failed';
        }
        Log::info('here 5');
        $payment->amount = $req->data->amount;
        Log::info('here 5.1');
        $payment->provider = $this->payment_provider;
        Log::info('here 5.2');
        $payment->provider_reference = $req->data->flw_ref;
        Log::info('here 5.3');
        Log::info("message");
        Log::info($req->data->created_at);
        Log::info(strtotime($req->data->created_at));
        Log::info(gettype(strtotime($req->data->created_at)));
        Log::info(gettype(now()));
        $payment->payment_date = strtotime($req->data->created_at);
        Log::info('here 5.4');
        $payment->save();
        Log::info('here 5.5');
        $this->user = $appointment->user()->first();
        Log::info($this->user);
        Log::info('here 5.6');
        $email = $this->user->email;
        Log::info('here 6');
        Mail::send($this->mail_template, ['user' => $this->user], function ($m) use ($email) {
            $m->from('dougieey1123@gmail.com', 'Payment Response!');

            $m->to($email, 'user name')->subject("Payment {$this->payment_status} for appointment  with Ref:{$this->clafiya_reference}");
        });
    }

    public function failed(\Throwable $exception)
    {

        $email = $this->user->email;
        Mail::send('emails.payments.index', ['user' => $this->user], function ($m) use ($email) {
            $m->from('dougieey1123@gmail.com', 'Clafiya Server error!');

            $m->to($email, 'user name')->subject("Server Error!! your most recent payment failed");
        });
        Log::error($exception);
    }
}

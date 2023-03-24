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
        if ($req->status !== 'success' || $req->data->status !== 'successful') {
            $this->payment_status = 'failed';
        } else {
            $this->payment_status = 'success';
        }
        if ($req->data->currency !== 'NGN' || $this->currency !== 'NGN') {
            $this->message = 'Payment failed because your payment was not in naira';
            $this->payment_status = 'failed';
        }
        $payment = Payment::where('clafiya_reference', $this->clafiya_reference)->first();
        if (!$payment) {
            $this->message = 'Payment failed because we could not find the payment you were trying to make';
            $this->payment_status = 'failed';
        }
        $appointment = Appointment::where('id', $payment->appointment_id)->first();
        if ($this->payment_status == 'success') {
            //do stuff
            $payment->status = 'success';
            $appointment->status = 1;
            $appointment->payment_id = $payment->id;
            $appointment->save();
            $this->mail_template = 'emails.payments.success';
        } else {
            $payment->status = 'failed';
            $this->mail_template = 'emails.payments.failed';
        }
        $payment->amount = $req->data->amount;
        $payment->provider = $this->payment_provider;
        $payment->provider_reference = $req->data->flw_ref;
        $payment->date = $req->data->created_at;
        $payment->save();
        $this->user = $appointment->user()->get();
        $email = $this->user->email;
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

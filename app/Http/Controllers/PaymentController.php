<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\Payment\Providers\Flutterwave\Flutterwave;
use App\Services\Payment\Providers\Paystack\Paystack;
use App\Jobs\Payments\Update as PaymentUpdateJob;
use App\Models\Payment;
use App\Models\Appointment;

class PaymentController extends Controller
{
    public function flutterwaveCardPayment(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'appointment_id' => ['required', 'string', 'exists:appointments,id,deleted_at,NULL'],
                'card_number' => ['required', 'size:16', 'string'],
                'cvv' => ['required', 'size:3', 'string'],
                'expiry_month' => ['required', 'string', 'min:1', 'max:2'],
                'expiry_year' => ['required', 'string', 'min:2', 'max:2'],
                'amount' => ['required', 'string'],
                'pin' => ['required', 'string', 'size:4'],

            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->respondBadRequest('Invalid or missing input fields', $validator->errors()->toArray());
            }

            $appointment = Appointment::find($request->appointment_id);
            if ($appointment->status == 1) {
                return $this->respondWithSuccess('You have already paid for this appointment!, Nice try though');
            }

            $transaction_reference = "clafiya" . date('Ymdhis');
            $payload = [
                "card_number" => $request->card_number,
                "cvv" => $request->cvv,
                "expiry_month" => $request->expiry_month,
                "expiry_year" => $request->expiry_year,
                "currency" => "NGN",
                "amount" => $request->amount,
                "email" => "lesliedouglas23@gmail.com",
                "fullname" => "Clafiya Developers",
                "tx_ref" => $transaction_reference,
                "redirect_url" => getenv('BACKEND_URL') . '/payments/verified/redirect'
            ];
            $encrypted_card_data = $this->encrypt(getenv('FLUTTERWAVE_ENCRYPTION_KEY'), $payload);
            $flutterwave = new Flutterwave;
            $response = $flutterwave->makeCardPayment($encrypted_card_data);
            if (isset($response->status_code) && ($response->status_code >= 400)) {
                $message = isset($response->message) ? $response->message : $response->data;
                return $this->respondBadRequest('Payment failed', $message);
            }
            if (isset($response->meta) && $response->meta->authorization->mode == 'pin') {
                //create a payment table entry here
                $payment = Payment::create([
                    'clafiya_reference' => $payload['tx_ref'],
                    'appointment_id' => $request->appointment_id,
                    'status' => 'pending',
                ]);
                $authentication = ["mode" => "pin", "pin" => $request->pin];
                $payload['authorization'] = $authentication;

                $encrypted_card_data_with_pin = $this->encrypt(getenv('FLUTTERWAVE_ENCRYPTION_KEY'), $payload);
                $response = $flutterwave->makeCardPayment($encrypted_card_data_with_pin);
                if (isset($response->status_code) && ($response->status_code >= 400)) {
                    $message = isset($response->message) ? $response->message : $response->data;
                    return $this->respondBadRequest('Payment failed', $message);
                }
                switch ($response->meta->authorization->mode) {
                    case 'otp':
                        //successful transaction
                        return $this->respondWithSuccess("Payment for {$response->data->amount} Naira is Pending!!...Go to our Card Payment Validation endpoint and finish up, your transaction Reference is {$response->data->flw_ref} ", $response->data->processor_response);
                        break;
                    case 'redirect':
                        //successful transaction
                        return $this->respondWithSuccess("Payment for {$response->data->amount} Naira is Pending!! Copy and Paste the link in the data field in your web browser or here on postman/insomniac to finish up", $response->meta->authorization->redirect);
                        break;
                    default:
                }
            } else if (isset($response->meta) && $response->meta->authorization->mode == "redirect") {
                $payment = Payment::create([
                    'clafiya_reference' => $payload['tx_ref'],
                    'appointment_id' => $request->appointment_id,
                    'status' => 'pending',
                ]);
                return $this->respondWithSuccess("Payment for {$response->data->amount} Naira is Pending!! Copy and Paste the link in the data field in your web browser or here on postman/insomniac to finish up", $response->meta->authorization->redirect);
            } else if (isset($response->meta) && $response->meta->authorization->mode == "avs_noauth") {
                return $this->respondBadRequest("Sorry, we do not support the Address Verification System payments at the moment, Use a different card that uses pin authentication or 3ds redirect");
            } else {
                return $this->respondBadRequest('Check your card details and try again');
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            return $this->respondInternalError('Oops, an error occurred. Please try again later.');
        }
    }


    public function flutterwaveVerifyAccount(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'account_number' => ['required', 'string'],
                'bank_code' => ['required', 'string'],
            ]);
            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->respondBadRequest('Invalid or missing input fields', $validator->errors()->toArray());
            }
            $account_number = $request->account_number;
            $bank_code = $request->bank_code;
            $flutterwave = new Flutterwave;
            $response = $flutterwave->validateAccountNumber($account_number, $bank_code);
            return $this->respondWithSuccess('Account verified', $response);
        } catch (\Exception $exception) {
            Log::error($exception);
            return $this->respondInternalError('Oops, an error occurred. Please try again later.');
        }
    }

    public function flutterwaveWebhook(Request $request)
    {
        try {
            $webhook_secret = config('app.env') == 'testing' ? 'testing_secret' : config('payment.providers.flutterwave.webhook_secret');

            $signature = $request->header('verif-hash');

            if (!$signature || ($signature !== $webhook_secret)) {
                Log::error("Webhook signature mismatch");
                return $this->respondWithSuccess('Payment received successfully');
            }

            if ($request->event == 'charge.completed') {
                $payload = [
                    'amount' => $request->data['amount'],
                    'clafiya_reference' => $request->data['tx_ref'],
                    'currency' => $request->data['currency'],
                    'payment_provider' => 'Flutterwave',
                    'payment_provider_reference' => $request->data['flw_ref'],
                    'verification_id' => $request->data['id'],
                    'status' => $request->data['status'],
                ];
                PaymentUpdateJob::dispatch($payload);
                Log::info("Webhook verified");
                return $this->respondWithSuccess('Webhook verified');
            }

            return $this->respondWithSuccess('Payment received successfully');
        } catch (\Exception $exception) {
            Log::error($exception);
            return $this->respondInternalError('Oops, an error occurred. Please try again later.');
        }
    }
    public function flutterwaveVerifyCardPayment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'otp' => ['required', 'string'],
                'transaction_reference' => ['required', 'string'],
            ]);
            if ($validator->fails()) {
                return $this->respondBadRequest('Invalid or missing input fields', $validator->errors()->toArray());
            }
            $payload = [
                "otp" => $request->otp,
                "flw_ref" => $request->transaction_reference,
            ];
            $flutterwave = new Flutterwave;
            $response = $flutterwave->verifyCardPayment($payload);
            return $this->respondWithSuccess('Payment is Successful', $response);
        } catch (\Exception $exception) {
            Log::error($exception);
            return $this->respondInternalError('Oops, an error occurred. Please try again later.');
        }
    }

    public function paystackMobilePayment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'appointment_id' => ['required', 'string', 'exists:appointments,id,deleted_at,NULL'],
                'phone' => ['required', 'string', 'min:9', 'max:11'],
                'provider' => ['required', 'string', 'in:mtn,vod,tgo'],
                'amount' => ['required'],
                'email' => ["required", "email"],
            ]);

            if ($validator->fails()) {
                return $this->respondBadRequest('Invalid or missing input fields', $validator->errors()->toArray());
            }
            $appointment = Appointment::find($request->appointment_id);
            if ($appointment->status == 1) {
                return $this->respondWithSuccess('You have already paid for this appointment!, Nice try though');
            }

            $currency = 'NGN';

            $payload = [
                'amount' => $request->amount,
                'email' => $request->email,
                'currency' => $currency,
                'mobile_money' => [
                    'phone' => $request->phone,
                    'provider' => $request->provider,
                ],
            ];
            $paystack = new Paystack;
            $response = $paystack->makeMobilePayment($payload);
            if ($response->status_code >= 400) {
                return $this->respondBadRequest('Sorry, there was an error, try a different method');
            }
            $transaction_reference = "clafiya" . date('Ymdhis');
            $payment = Payment::create([
                'clafiya_reference' => $transaction_reference,
                'appointment_id' => $request->appointment_id,
                'status' => 'pending',
            ]);
            return $this->respondWithSuccess(
                'Your payment was successful',
                ['data' => $response->data->display_text]
            );
        } catch (\Exception $exception) {
            Log::error($exception);
            return $this->respondInternalError('Oops, an error occurred. Please try again later.');
        }
    }

    public function paystackUssdPayment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'appointment_id' => ['required', 'string', 'exists:appointments,id,deleted_at,NULL'],
                'type' => ['required', 'string', 'size:3',],
                'amount' => ['required'],
                'email' => ["required", "email"],
            ]);

            if ($validator->fails()) {
                return $this->respondBadRequest('Invalid or missing input fields', $validator->errors()->toArray());
            }
            $appointment = Appointment::find($request->appointment_id);
            if ($appointment->status == 1) {
                return $this->respondWithSuccess('You have already paid for this appointment!, Nice try though');
            }
            $transaction_reference = "clafiya" . date('Ymdhis');
            $payment = Payment::create([
                'clafiya_reference' => $transaction_reference,
                'appointment_id' => $request->appointment_id,
                'status' => 'pending',
            ]);
            $payload = [
                'email' => $request->email,
                'amount' => $request->amount,
                'ussd' => [
                    'type' => $request->type,
                ],
                "metadata" => [
                    "custom_fields" => [
                        [
                            "clafiya_reference" => $transaction_reference,
                        ]
                    ]
                ],
            ];
            $paystack = new Paystack;
            $response = $paystack->makeUssdPayment($payload);
            if (!$response->status || (isset($response->status_code) && $response->status_code >= 400)) {
                return $this->respondBadRequest('Sorry, there was an error, try a different method');
            }

            return $this->respondWithSuccess(
                'Your payment was successful',
                ['data' => $response->data->display_text]
            );
        } catch (\Exception $exception) {
            Log::error($exception);
            return $this->respondInternalError('Oops, an error occurred. Please try again later.');
        }
    }

    public function paystackWebhook(Request $request)
    {
        try {
            $webhook_secret = config('app.env') == 'testing' ? 'testing_secret' : config('payment.providers.flutterwave.webhook_secret');

            $signature = $request->header('verif-hash');

            if (!$signature || ($signature !== $webhook_secret)) {
                Log::error("Webhook signature mismatch");
                return $this->respondWithSuccess('Payment received successfully');
            }

            if ($request->event == 'charge.completed') {
                $payload = [
                    'amount' => $request->data['amount'],
                    'clafiya_reference' => $request->data['tx_ref'],
                    'currency' => $request->data['currency'],
                    'payment_provider' => 'Flutterwave',
                    'payment_provider_reference' => $request->data['flw_ref'],
                    'verification_id' => $request->data['id'],
                    'status' => $request->data['status'],
                ];
                PaymentUpdateJob::dispatch($payload);
                Log::info("Webhook verified");
                return $this->respondWithSuccess('Webhook verified');
            }

            return $this->respondWithSuccess('Payment received successfully');
        } catch (\Exception $exception) {
            Log::error($exception);
            return $this->respondInternalError('Oops, an error occurred. Please try again later.');
        }
    }

    protected function encrypt(string $encryptionKey, array $payload)
    {
        Log::info('encrypt');
        Log::info($encryptionKey);
        Log::info($payload);
        $encrypted = openssl_encrypt(json_encode($payload), 'DES-EDE3', $encryptionKey, OPENSSL_RAW_DATA);
        return base64_encode($encrypted);
    }
}

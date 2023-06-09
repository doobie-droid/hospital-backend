<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Appointment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;


class AppointmentController extends Controller
{
    public function get(Request $request)
    {
        try {
            $user = auth()->user();

            $unpaid_appointments = $user->appointments()->where('status', 0)->get();
            $paid_appointments = $user->appointments()->where('status', 1)->get();

            return $this->respondWithSuccess(
                'Get Appointment successful',
                [
                    'unpaid_appointments' => $unpaid_appointments,
                    'paid_appointments' => $paid_appointments,
                ]
            );
        } catch (\Exception $exception) {
            Log::error($exception);
            return $this->respondInternalError('Oops, an error occurred. Please try again later.');
        }
    }
    public function create(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string', 'max:255'],
                'appointment_date' => ['required', 'after_or_equal:tomorrow', 'date', 'max:255'],
            ]);


            if ($validator->fails()) {
                return $this->respondBadRequest('Invalid or missing input fields', $validator->errors()->toArray());
            }

            $appointment = Appointment::create([
                'name' => $request->name,
                'user_id' => auth()->user()->id,
                'description' => $request->description,
                'appointment_date' => $request->appointment_date,
                'status' => 0,
            ]);
            $appointment->status = 'pending';
            $user = auth()->user();
            $email = $user->email;
            Mail::send('emails.appointments.welcome', ['user' => auth()->user()], function ($m) use ($email) {
                $m->from('dougieey1123@gmail.com', 'Booking Process Started!');

                $m->to($email, 'user name')->subject('Thanks for using Clafiya!');
            });
            return $this->respondWithSuccess('Appointment successfully created', [
                'appointment' => $appointment,
            ]);
        } catch (\Exception $exception) {
            Log::error($exception);
            return $this->respondInternalError('Oops, an error occurred. Please try again later.');
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make(array_merge($request->all(), ['id' => $id]), [
                'id' => ['required', 'string', 'exists:appointments,id,deleted_at,NULL'],
                'name' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string', 'max:255'],
                'appointment_date' => ['required', 'after_or_equal:tomorrow', 'date', 'max:255'],
            ]);


            if ($validator->fails()) {
                return $this->respondBadRequest('Invalid or missing input fields', $validator->errors()->toArray());
            }
            Appointment::whereId($id)->update($request->all());
            $user = auth()->user();
            $email = $user->email;
            Mail::send('emails.appointments.update', ['user' => auth()->user()], function ($m) use ($email) {
                $m->from('dougieey1123@gmail.com', 'Booking Updated!');

                $m->to($email, 'user name')->subject('Thanks for using Clafiya!');
            });
            return $this->respondWithSuccess('Update appointment successful', [
                'appointment' => Appointment::whereId($id)->first(),
            ]);
        } catch (\Exception $exception) {
            Log::error($exception);
            return $this->respondInternalError('Oops, an error occurred. Please try again later.');
        }
    }
    public function delete(Request $request, $id)
    {
        try {
            $validator = Validator::make(['id' => $id], [
                'id' => ['required', 'string', 'exists:appointments,id,deleted_at,NULL'],
            ]);

            if ($validator->fails()) {
                return $this->respondBadRequest('Invalid or missing input fields', $validator->errors()->toArray());
            }
            $appointment = Appointment::find($id);
            $appointment_deleted = $appointment->delete(); //returns true/false
            if ($appointment_deleted) {
                $user = auth()->user();
                $email = $user->email;
                Mail::send('emails.appointments.delete', ['user' => auth()->user()], function ($m) use ($email) {
                    $m->from('dougieey1123@gmail.com', 'Booking Deleted!');

                    $m->to($email, 'user name')->subject('Cancelled Clafiya Appointment!');
                });
                return $this->respondWithSuccess('Delete successful',);
            }
            return $this->respondWithSuccess('Delete failed');
        } catch (\Exception $exception) {
            Log::error($exception);
            return $this->respondInternalError('Oops, an error occurred. Please try again later.');
        }
    }
    public function getAll(Request $request)
    {
        try {
            $appointments = Appointment::all();
            return $this->respondWithSuccess(
                'Get all appointments successful',
                [
                    'appointments' => $appointments,
                ]
            );
        } catch (\Exception $exception) {
            Log::error($exception);
            return $this->respondInternalError('Oops, an error occurred. Please try again later.');
        }
    }
}

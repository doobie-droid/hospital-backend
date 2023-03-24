<?php

namespace Tests\Unit;

use Illuminate\Http\Response;
use Tests\TestCase;
use App\Models;
use Illuminate\Support\Facades\Artisan;

class AppointmentTest extends TestCase
{
    public $user;
    public $payload;

    public function setUp(): void
    {

        parent::setUp();
        Artisan::call('migrate:refresh');
        $this->user = Models\User::factory()->verified()->create();
        $this->payload = [
            "name" => "Syphilis",
            "description" => "I have been pissing seriously",
            "appointment_date" => "2023-03-30"
        ];
    }

    public function testUserCanBookAppointment()
    {

        $response = $this->actingAs($this->user, 'api')->post('/api/appointments', $this->payload);


        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('appointments', [
            "name" => "Syphilis",
            "description" => "I have been pissing seriously",
            "appointment_date" => "2023-03-30"
        ]);
    }

    public function testUserCanViewTheirAppointments()
    {

        $response = $this->actingAs($this->user, 'api')->post('/api/appointments', $this->payload);
        $response = $this->actingAs($this->user, 'api')->get('/api/appointments');
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testUserCannotViewTheirAppointmentsUnlessAuthenticated()
    {
        $user = Models\User::factory()->create();
        $response = $this->get('/api/appointments');
        $response->assertStatus(500);
    }

    public function testUserCanUpdateTheirAppointments()
    {
        $response = $this->actingAs($this->user, 'api')->post('/api/appointments', $this->payload);
        $appointment_id = $response->json()['data']['appointment']['id'];
        $new_payload = [
            "name" => "Gonorrhoea",
            "description" => "Itching all over",
            "appointment_date" => "2029-04-04"
        ];

        $new_response = $this->actingAs($this->user, 'api')->patch("/api/appointments/{$appointment_id}", $new_payload);

        $new_response->assertStatus(200);
        $this->assertDatabaseMissing('appointments', [
            "name" => "Syphilis",
            "description" => "I have been pissing seriously",
            "appointment_date" => "2023-03-30"
        ]);
        $this->assertDatabaseHas('appointments', [
            "name" => "Gonorrhoea",
            "description" => "Itching all over",
            "appointment_date" => "2029-04-04"
        ]);
    }

    public function testUserCanDeleteTheirAppointments()
    {
        $response = $this->actingAs($this->user, 'api')->post('/api/appointments', $this->payload);
        $this->assertDatabaseHas('appointments', [
            "name" => "Syphilis",
            "description" => "I have been pissing seriously",
            "appointment_date" => "2023-03-30"
        ]);
        $appointment_id = $response->json()['data']['appointment']['id'];
        // dd($appointment_id);
        $new_response = $this->actingAs($this->user, 'api')->delete("/api/appointments/{$appointment_id}");
        $new_response->assertStatus(200);
        // $this->assertDatabaseMissing('appointments', [
        //     "name" => "Syphilis",
        //     "description" => "I have been pissing seriously",
        //     "appointment_date" => "2023-03-30"
        // ]);
    }
}

<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Constants\Roles;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AppointmentTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_the_appointment_creation_works(): void
    {
        $user = User::factory()->count(1)->create();
        Log::info($user);

        // $this->be($user);

        // $request = MockData\Collection::generateStandardCreateRequest();
        // $request['digiverse_id'] = Models\Collection::factory()
        //     ->for($user, 'owner')
        //     ->create()->id;
        // $expected_response_structure = MockData\Collection::generateCollectionCreatedResponse();
        // $response = $this->json('POST', '/api/v1/collections', $request);
        // $this->assertTrue(true);
    }
}

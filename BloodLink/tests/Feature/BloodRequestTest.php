<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Donor;
use App\Models\Hospital;
use App\Models\BloodRequest;
use App\Models\Donation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BloodRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Hospital $hospital;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'hospital',
            'email_verified_at' => now(),
        ]);

        $this->hospital = Hospital::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    protected function headers(): array
    {
        return ['Authorization' => 'Bearer ' . $this->token];
    }

    public function test_create_blood_request()
    {
        $response = $this->postJson('/api/requests', [
            'hospital_id' => $this->hospital->id,
            'blood_type' => 'O+',
            'quantity' => 500,
            'urgency' => 'high',
            'location' => 'New York Hospital',
        ], $this->headers());

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('blood_requests', [
            'hospital_id' => $this->hospital->id,
            'blood_type' => 'O+',
            'quantity' => 500,
        ]);
    }

    public function test_list_blood_requests()
    {
        BloodRequest::factory(5)->create(['hospital_id' => $this->hospital->id]);

        $response = $this->getJson('/api/requests', $this->headers());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_show_blood_request()
    {
        $request = BloodRequest::factory()->create(['hospital_id' => $this->hospital->id]);

        $response = $this->getJson("/api/requests/{$request->id}", $this->headers());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_update_blood_request()
    {
        $request = BloodRequest::factory()->create([
            'hospital_id' => $this->hospital->id,
            'status' => 'open',
        ]);

        $response = $this->putJson("/api/requests/{$request->id}", [
            'status' => 'fulfilled',
        ], $this->headers());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertEquals('fulfilled', $request->fresh()->status);
    }

    public function test_mark_urgent()
    {
        $request = BloodRequest::factory()->create([
            'hospital_id' => $this->hospital->id,
            'urgency' => 'low',
        ]);

        $response = $this->putJson("/api/requests/{$request->id}/urgent", [], $this->headers());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertEquals('critical', $request->fresh()->urgency);
    }

    public function test_mark_completed()
    {
        $request = BloodRequest::factory()->create([
            'hospital_id' => $this->hospital->id,
            'status' => 'open',
        ]);

        $response = $this->putJson("/api/requests/{$request->id}/complete", [], $this->headers());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertEquals('fulfilled', $request->fresh()->status);
    }

    public function test_get_compatible_donors()
    {
        // Create donors with O+ blood (compatible with O+)
        Donor::factory()->create(['blood_type' => 'O+', 'availability' => true]);
        Donor::factory()->create(['blood_type' => 'O+', 'availability' => true]);

        // Create donor with A+ (not compatible with O+)
        Donor::factory()->create(['blood_type' => 'A+', 'availability' => true]);

        $request = BloodRequest::factory()->create([
            'hospital_id' => $this->hospital->id,
            'blood_type' => 'O+',
        ]);

        $response = $this->getJson("/api/requests/{$request->id}/donors", $this->headers());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_delete_blood_request()
    {
        $request = BloodRequest::factory()->create(['hospital_id' => $this->hospital->id]);

        $response = $this->deleteJson("/api/requests/{$request->id}", [], $this->headers());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertModelMissing($request);
    }

    public function test_filter_requests_by_blood_type()
    {
        BloodRequest::factory()->create(['hospital_id' => $this->hospital->id, 'blood_type' => 'O+']);
        BloodRequest::factory()->create(['hospital_id' => $this->hospital->id, 'blood_type' => 'A+']);

        $response = $this->getJson('/api/requests?blood_type=O+', $this->headers());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_create_request_fails_with_invalid_data()
    {
        $response = $this->postJson('/api/requests', [
            'hospital_id' => 999,
            'blood_type' => 'invalid',
        ], $this->headers());

        $response->assertStatus(422);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Donor;
use App\Models\Donation;
use App\Models\BloodRequest;
use App\Models\Hospital;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonorTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Donor $donor;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'donor',
            'email_verified_at' => now(),
        ]);

        $this->donor = Donor::factory()->create([
            'user_id' => $this->user->id,
            'blood_type' => 'O+',
            'city' => 'New York',
            'availability' => true,
        ]);

        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    protected function headers(): array
    {
        return ['Authorization' => 'Bearer ' . $this->token];
    }

    public function test_list_donors()
    {
        Donor::factory(5)->create();

        $response = $this->getJson('/api/donors', $this->headers());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_show_donor()
    {
        $response = $this->getJson("/api/donors/{$this->donor->id}", $this->headers());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_update_donor()
    {
        $response = $this->putJson("/api/donors/{$this->donor->id}", [
            'city' => 'Los Angeles',
            'availability' => false,
        ], $this->headers());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertEquals('Los Angeles', $this->donor->fresh()->city);
        $this->assertFalse($this->donor->fresh()->availability);
    }

    public function test_search_donors_by_blood_type()
    {
        $response = $this->getJson('/api/donors-search?blood_type=O+', $this->headers());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_get_nearby_donors()
    {
        $response = $this->getJson('/api/donors-nearby?latitude=40.7128&longitude=-74.0060&distance=50', $this->headers());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_get_donation_history()
    {
        Donation::factory(3)->create(['donor_id' => $this->donor->id]);

        $response = $this->getJson("/api/donors/{$this->donor->id}/history", $this->headers());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_get_pending_requests()
    {
        $hospital = Hospital::factory()->create();
        BloodRequest::factory(3)->create([
            'hospital_id' => $hospital->id,
            'blood_type' => 'O+',
            'status' => 'open',
        ]);

        $response = $this->getJson("/api/donors/{$this->donor->id}/requests", $this->headers());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_respond_to_blood_request()
    {
        $hospital = Hospital::factory()->create();
        $bloodRequest = BloodRequest::factory()->create([
            'hospital_id' => $hospital->id,
            'blood_type' => 'O+',
            'status' => 'open',
        ]);

        $response = $this->postJson("/api/donors/{$this->donor->id}/respond/{$bloodRequest->id}", [
            'status' => 'accepted',
        ], $this->headers());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_delete_donor()
    {
        $response = $this->deleteJson("/api/donors/{$this->donor->id}", [], $this->headers());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertModelMissing($this->donor);
    }
}

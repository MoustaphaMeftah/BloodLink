<?php

namespace Tests\Feature;

use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Donor $donor;

    private Hospital $hospital;

    private BloodRequest $bloodRequest;

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
            'availability' => true,
            'last_donation_date' => null,
        ]);

        $hospitalUser = User::factory()->create(['role' => 'hospital', 'email_verified_at' => now()]);
        $this->hospital = Hospital::factory()->create(['user_id' => $hospitalUser->id]);
        $this->bloodRequest = BloodRequest::factory()->create([
            'hospital_id' => $this->hospital->id,
            'blood_type' => 'O+',
            'status' => 'open',
        ]);

        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    protected function headers(): array
    {
        return ['Authorization' => 'Bearer '.$this->token];
    }

    public function test_record_donation()
    {
        $response = $this->postJson('/api/donations', [
            'donor_id' => $this->donor->id,
            'blood_request_id' => $this->bloodRequest->id,
            'donation_date' => now()->format('Y-m-d'),
            'quantity' => 450,
        ], $this->headers());

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('donations', [
            'donor_id' => $this->donor->id,
            'quantity' => 450,
        ]);

        $this->assertNotNull($this->donor->fresh()->last_donation_date);
    }

    public function test_donation_fails_with_invalid_quantity()
    {
        $response = $this->postJson('/api/donations', [
            'donor_id' => $this->donor->id,
            'blood_request_id' => $this->bloodRequest->id,
            'donation_date' => now()->format('Y-m-d'),
            'quantity' => 50,
        ], $this->headers());

        $response->assertStatus(422);
    }

    public function test_donation_history()
    {
        Donation::factory(3)->create(['donor_id' => $this->donor->id]);

        $response = $this->getJson("/api/donors/{$this->donor->id}/donations", $this->headers());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_donation_eligibility()
    {
        $response = $this->getJson("/api/donors/{$this->donor->id}/eligibility", $this->headers());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $data = $response->json('data');
        $this->assertTrue($data['eligible']);
    }

    public function test_donation_not_eligible_if_recently_donated()
    {
        $this->donor->update(['last_donation_date' => now()->subDays(10)]);

        $response = $this->getJson("/api/donors/{$this->donor->id}/eligibility", $this->headers());

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertFalse($data['eligible']);
    }

    public function test_donation_not_eligible_if_unavailable()
    {
        $this->donor->update(['availability' => false]);

        $response = $this->getJson("/api/donors/{$this->donor->id}/eligibility", $this->headers());

        $data = $response->json('data');
        $this->assertFalse($data['eligible']);
    }
}

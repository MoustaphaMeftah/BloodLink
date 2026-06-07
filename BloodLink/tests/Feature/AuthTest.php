<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Donor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_register_as_donor()
    {
        $response = $this->postJson('/api/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'donor',
            'city' => 'New York',
            'blood_type' => 'O+',
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com', 'role' => 'donor']);
        $this->assertDatabaseHas('donors', ['blood_type' => 'O+']);
    }

    public function test_user_can_register_as_hospital()
    {
        $response = $this->postJson('/api/register', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@hospital.com',
            'phone' => '0987654321',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'hospital',
            'city' => 'Los Angeles',
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', ['email' => 'jane@hospital.com', 'role' => 'hospital']);
        $this->assertDatabaseHas('hospitals', ['phone' => '0987654321']);
    }

    public function test_registration_fails_with_invalid_data()
    {
        $response = $this->postJson('/api/register', [
            'first_name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'not-matching',
            'role' => 'invalid',
        ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_registration_fails_with_duplicate_email()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'existing@example.com',
            'phone' => '1234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'donor',
            'city' => 'New York',
            'blood_type' => 'O+',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['token']);
    }

    public function test_login_fails_with_unverified_email()
    {
        $user = User::factory()->create([
            'email' => 'unverified@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'unverified@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJson(['message' => 'Please verify your email first']);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_email_verification()
    {
        $code = 'test-verification-code-123';
        $user = User::factory()->create([
            'email_verified_at' => null,
            'verification_code' => $code,
        ]);

        $response = $this->getJson("/email/verify/{$code}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertNull($user->fresh()->verification_code);
    }

    public function test_email_verification_fails_with_invalid_code()
    {
        $response = $this->getJson('/email/verify/invalid-code');

        $response->assertStatus(401);
    }

    public function test_password_reset_flow()
    {
        $user = User::factory()->create(['email' => 'reset@example.com']);

        $response = $this->postJson('/api/forgot-password', [
            'email' => 'reset@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertNotNull($user->fresh()->password_reset_token);

        $token = $user->fresh()->password_reset_token;

        $response = $this->postJson('/api/reset-password', [
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_get_authenticated_user()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_unauthenticated_access_is_blocked()
    {
        $response = $this->getJson('/api/user');
        $response->assertStatus(401);
    }
}

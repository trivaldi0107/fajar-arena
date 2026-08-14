<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register_and_redirect_to_otp(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $response->assertRedirect(route('register.verify_otp'));
        $this->assertNotNull(session('pending_otp_user_id'));
    }

    public function test_user_can_verify_otp_and_authenticate(): void
    {
        $user = User::create([
            'name' => 'Verify User',
            'email' => 'verify@example.com',
            'password' => 'password123',
            'otp_code' => '654321',
            'otp_expires_at' => now()->addMinutes(10),
            'email_verified_at' => null,
        ]);

        $response = $this->withSession(['pending_otp_user_id' => $user->id])
            ->post('/register/verify-otp', [
                'otp_code' => '654321',
            ]);

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->email_verified_at);
        $response->assertRedirect(route('portal'));
    }
}

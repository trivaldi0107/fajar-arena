<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_creation_with_admin_role(): void
    {
        $user = User::create([
            'name' => 'Admin Fajar',
            'email' => 'admin@fajararena.com',
            'password' => 'secret123',
            'role' => 'admin',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@fajararena.com',
            'role' => 'admin',
        ]);

        $this->assertEquals('admin', $user->role);
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_user_creation_with_default_user_role(): void
    {
        $user = User::create([
            'name' => 'Customer',
            'email' => 'user@example.com',
            'password' => 'password123',
            'role' => 'user',
        ]);

        $this->assertEquals('user', $user->role);
    }

    public function test_sensitive_attributes_are_hidden_in_array(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
            'otp_code' => '123456',
        ]);

        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
        $this->assertArrayNotHasKey('otp_code', $array);
    }

    public function test_otp_dates_are_cast_to_datetime(): void
    {
        $now = now();
        $user = User::factory()->create([
            'otp_expires_at' => $now,
        ]);

        $this->assertInstanceOf(\DateTimeInterface::class, $user->otp_expires_at);
    }
}

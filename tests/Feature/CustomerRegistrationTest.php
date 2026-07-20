<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_loads(): void
    {
        $this->get('/register')->assertOk()->assertSee('Daftar', false);
    }

    public function test_registration_creates_customer_and_logs_in(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi', 'email' => 'budi@example.com',
            'password' => 'rahasia123', 'password_confirmation' => 'rahasia123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $user = User::where('email', 'budi@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('customer', $user->division);
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'ada@example.com']);

        $response = $this->post('/register', [
            'name' => 'Ada', 'email' => 'ada@example.com',
            'password' => 'rahasia123', 'password_confirmation' => 'rahasia123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $response = $this->post('/register', [
            'name' => 'Ada', 'email' => 'ada@example.com',
            'password' => 'rahasia123', 'password_confirmation' => 'beda',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }
}

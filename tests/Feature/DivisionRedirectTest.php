<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DivisionRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_login_redirects_to_dashboard(): void
    {
        User::factory()->create(['email' => 'c@example.com', 'password' => Hash::make('rahasia123'), 'division' => 'customer']);

        $this->post('/login', ['email' => 'c@example.com', 'password' => 'rahasia123'])
            ->assertRedirect('/dashboard');
    }

    public function test_staff_login_redirects_to_admin(): void
    {
        User::factory()->create(['email' => 's@example.com', 'password' => Hash::make('rahasia123'), 'division' => 'super_admin']);

        $this->post('/login', ['email' => 's@example.com', 'password' => 'rahasia123'])
            ->assertRedirect(route('admin.bookings.index'));
    }

    public function test_customer_cannot_reach_admin(): void
    {
        $customer = User::factory()->create(['division' => 'customer']);

        $this->actingAs($customer)->get('/admin/bookings')->assertRedirect('/dashboard');
    }

    public function test_staff_can_reach_admin(): void
    {
        $admin = User::factory()->create(['division' => 'super_admin']);

        // Bukan di-redirect ke /dashboard oleh middleware staff (status apa pun kecuali redirect ke dashboard).
        $response = $this->actingAs($admin)->get('/admin/bookings');
        $this->assertNotSame('/dashboard', $response->headers->get('Location'));
    }
}

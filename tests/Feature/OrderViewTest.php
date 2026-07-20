<?php

namespace Tests\Feature;

use App\Models\InvitationTemplate;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderViewTest extends TestCase
{
    use RefreshDatabase;

    private function order(User $owner): Order
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $t = InvitationTemplate::create([
            'name' => 'Mawar', 'slug' => 't'.uniqid(), 'status' => 'published',
            'price' => 500000, 'created_by' => $admin->id,
        ]);

        return Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $owner->id, 'invitation_template_id' => $t->id,
            'price' => 500000, 'status' => Order::STATUS_PENDING,
        ]);
    }

    public function test_owner_sees_own_order(): void
    {
        $owner = User::factory()->create(['division' => 'customer']);
        $this->actingAs($owner)->get(route('orders.show', $this->order($owner)))
            ->assertOk()->assertSee('Mawar');
    }

    public function test_other_customer_forbidden(): void
    {
        $owner = User::factory()->create(['division' => 'customer']);
        $other = User::factory()->create(['division' => 'customer']);

        $this->actingAs($other)->get(route('orders.show', $this->order($owner)))->assertForbidden();
    }

    public function test_staff_can_view_any_order(): void
    {
        $owner = User::factory()->create(['division' => 'customer']);
        $staff = User::factory()->create(['division' => 'super_admin']);

        $this->actingAs($staff)->get(route('orders.show', $this->order($owner)))->assertOk();
    }

    public function test_index_lists_only_own_orders(): void
    {
        $owner = User::factory()->create(['division' => 'customer']);
        $mine = $this->order($owner);
        $theirs = $this->order(User::factory()->create(['division' => 'customer']));

        $this->actingAs($owner)->get(route('orders.index'))
            ->assertOk()
            ->assertSee($mine->order_number)
            ->assertDontSee($theirs->order_number);
    }

    public function test_show_proof_owner_ok_other_forbidden(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create(['division' => 'customer']);
        $order = $this->order($owner);
        $this->actingAs($owner)->post(route('orders.proof.upload', $order), [
            'bukti' => UploadedFile::fake()->image('b.jpg'),
        ]);

        $this->actingAs($owner)->get(route('orders.proof.show', $order))->assertOk();
        $this->actingAs(User::factory()->create(['division' => 'customer']))
            ->get(route('orders.proof.show', $order))->assertForbidden();
    }
}

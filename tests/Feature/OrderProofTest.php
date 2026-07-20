<?php

namespace Tests\Feature;

use App\Models\InvitationTemplate;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderProofTest extends TestCase
{
    use RefreshDatabase;

    private function order(User $owner, string $status = Order::STATUS_PENDING): Order
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $t = InvitationTemplate::create([
            'name' => 'T', 'slug' => 't'.uniqid(), 'status' => 'published',
            'price' => 500000, 'created_by' => $admin->id,
        ]);

        return Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $owner->id, 'invitation_template_id' => $t->id,
            'price' => 500000, 'status' => $status,
        ]);
    }

    public function test_owner_uploads_proof_to_local_disk(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create(['division' => 'customer']);
        $order = $this->order($owner);

        $this->actingAs($owner)->post(route('orders.proof.upload', $order), [
            'bukti' => UploadedFile::fake()->image('bukti.jpg'),
        ])->assertRedirect(route('orders.show', $order));

        $order->refresh();
        $this->assertSame(Order::STATUS_AWAITING, $order->status);
        $this->assertNotNull($order->payment_proof_path);
        Storage::disk('local')->assertExists($order->payment_proof_path);
    }

    public function test_non_image_rejected(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create(['division' => 'customer']);
        $order = $this->order($owner);

        $this->actingAs($owner)->post(route('orders.proof.upload', $order), [
            'bukti' => UploadedFile::fake()->create('x.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors('bukti');
    }

    public function test_non_owner_cannot_upload(): void
    {
        $owner = User::factory()->create(['division' => 'customer']);
        $other = User::factory()->create(['division' => 'customer']);
        $order = $this->order($owner);

        $this->actingAs($other)->post(route('orders.proof.upload', $order), [
            'bukti' => UploadedFile::fake()->image('b.jpg'),
        ])->assertForbidden();
    }

    public function test_cannot_upload_when_paid(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create(['division' => 'customer']);
        $order = $this->order($owner, Order::STATUS_PAID);

        $this->actingAs($owner)->post(route('orders.proof.upload', $order), [
            'bukti' => UploadedFile::fake()->image('b.jpg'),
        ])->assertForbidden();
    }
}

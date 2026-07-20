<?php

namespace Tests\Feature;

use App\Models\InvitationTemplate;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderModelTest extends TestCase
{
    use RefreshDatabase;

    private function order(array $attrs = []): Order
    {
        $admin = User::factory()->create(['division' => 'super_admin']);
        $slug = 't-' . uniqid();
        $template = InvitationTemplate::create([
            'name' => 'T', 'slug' => $slug, 'status' => 'published',
            'price' => 500000, 'created_by' => $admin->id,
        ]);

        return Order::create(array_merge([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $admin->id,
            'invitation_template_id' => $template->id,
            'price' => 500000,
            'status' => Order::STATUS_PENDING,
        ], $attrs));
    }

    public function test_status_label_indonesian(): void
    {
        $this->assertSame('Menunggu pembayaran', $this->order()->statusLabel());
        $this->assertSame('Lunas', $this->order(['status' => Order::STATUS_PAID])->statusLabel());
    }

    public function test_price_label_rupiah(): void
    {
        $this->assertSame('Rp500.000', $this->order()->priceLabel());
    }

    public function test_can_upload_proof_only_before_final(): void
    {
        $this->assertTrue($this->order(['status' => Order::STATUS_PENDING])->canUploadProof());
        $this->assertTrue($this->order(['status' => Order::STATUS_AWAITING])->canUploadProof());
        $this->assertFalse($this->order(['status' => Order::STATUS_PAID])->canUploadProof());
        $this->assertFalse($this->order(['status' => Order::STATUS_CANCELLED])->canUploadProof());
    }

    public function test_generate_order_number_format_and_unique(): void
    {
        $a = Order::generateOrderNumber();
        $this->assertMatchesRegularExpression('/^ORD-\d{8}-[A-Z0-9]{5}$/', $a);

        $order = $this->order();
        $this->assertNotSame($order->order_number, Order::generateOrderNumber());
    }
}

<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankAccountAdminTest extends TestCase
{
    use RefreshDatabase;

    private function staff(): User
    {
        return User::factory()->create(['division' => 'super_admin']);
    }

    public function test_staff_creates_bank_account(): void
    {
        $this->actingAs($this->staff())->post(route('admin.bank-accounts.store'), [
            'bank_name' => 'BCA', 'account_number' => '1234567890', 'account_holder' => 'PT Luminara', 'is_active' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('bank_accounts', ['bank_name' => 'BCA', 'account_number' => '1234567890', 'is_active' => true]);
    }

    public function test_validation_requires_fields(): void
    {
        $this->actingAs($this->staff())->post(route('admin.bank-accounts.store'), [])
            ->assertSessionHasErrors(['bank_name', 'account_number', 'account_holder']);
    }

    public function test_staff_updates_bank_account(): void
    {
        $bank = BankAccount::create(['bank_name' => 'BCA', 'account_number' => '111', 'account_holder' => 'L', 'is_active' => true]);

        $this->actingAs($this->staff())->put(route('admin.bank-accounts.update', $bank), [
            'bank_name' => 'BCA', 'account_number' => '999', 'account_holder' => 'L',
        ])->assertRedirect();

        $this->assertDatabaseHas('bank_accounts', ['id' => $bank->id, 'account_number' => '999', 'is_active' => false]);
    }

    public function test_staff_deletes_bank_account(): void
    {
        $bank = BankAccount::create(['bank_name' => 'BCA', 'account_number' => '111', 'account_holder' => 'L', 'is_active' => true]);

        $this->actingAs($this->staff())->delete(route('admin.bank-accounts.destroy', $bank))->assertRedirect();
        $this->assertDatabaseMissing('bank_accounts', ['id' => $bank->id]);
    }

    public function test_customer_blocked(): void
    {
        $customer = User::factory()->create(['division' => 'customer']);
        $this->actingAs($customer)->get(route('admin.bank-accounts.index'))->assertRedirect(route('dashboard'));
    }
}

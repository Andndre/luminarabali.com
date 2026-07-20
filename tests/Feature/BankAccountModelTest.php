<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankAccountModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_scope_filters_inactive(): void
    {
        BankAccount::create(['bank_name' => 'BCA', 'account_number' => '111', 'account_holder' => 'Luminara', 'is_active' => true]);
        BankAccount::create(['bank_name' => 'Mandiri', 'account_number' => '222', 'account_holder' => 'Luminara', 'is_active' => false]);

        $active = BankAccount::active()->get();

        $this->assertCount(1, $active);
        $this->assertSame('BCA', $active->first()->bank_name);
    }
}

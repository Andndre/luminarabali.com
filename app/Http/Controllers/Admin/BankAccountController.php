<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $accounts = BankAccount::latest()->get();

        return view('admin.bank-accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('admin.bank-accounts.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        BankAccount::create($data);

        return redirect()->route('admin.bank-accounts.index')->with('success', 'Rekening ditambahkan.');
    }

    public function edit(BankAccount $bankAccount)
    {
        return view('admin.bank-accounts.edit', ['account' => $bankAccount]);
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $bankAccount->update($this->validated($request));

        return redirect()->route('admin.bank-accounts.index')->with('success', 'Rekening diperbarui.');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        return redirect()->route('admin.bank-accounts.index')->with('success', 'Rekening dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_holder' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);
        // Checkbox tak tercentang tak terkirim → default nonaktif.
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}

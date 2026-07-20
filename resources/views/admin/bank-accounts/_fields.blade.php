<div>
    <label class="mb-1 block text-sm font-medium text-gray-700">Nama Bank</label>
    <input type="text" name="bank_name" value="{{ old('bank_name', $account->bank_name ?? '') }}" class="w-full rounded-lg border px-3 py-2">
    @error('bank_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium text-gray-700">Nomor Rekening</label>
    <input type="text" name="account_number" value="{{ old('account_number', $account->account_number ?? '') }}" class="w-full rounded-lg border px-3 py-2">
    @error('account_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium text-gray-700">Atas Nama</label>
    <input type="text" name="account_holder" value="{{ old('account_holder', $account->account_holder ?? '') }}" class="w-full rounded-lg border px-3 py-2">
    @error('account_holder') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>
<label class="flex items-center gap-2 text-sm">
    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $account->is_active ?? true) ? 'checked' : '' }}>
    Aktif (ditampilkan ke customer)
</label>

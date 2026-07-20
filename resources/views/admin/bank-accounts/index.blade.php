@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Rekening Bank</h1>
        <a href="{{ route('admin.bank-accounts.create') }}" class="rounded-lg bg-black px-4 py-2 font-bold text-white hover:bg-gray-800">Tambah Rekening</a>
    </div>

    <div class="overflow-x-auto rounded-2xl border bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr><th class="px-4 py-3">Bank</th><th class="px-4 py-3">Nomor</th><th class="px-4 py-3">Atas Nama</th><th class="px-4 py-3">Aktif</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($accounts as $account)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $account->bank_name }}</td>
                        <td class="px-4 py-3">{{ $account->account_number }}</td>
                        <td class="px-4 py-3">{{ $account->account_holder }}</td>
                        <td class="px-4 py-3">{{ $account->is_active ? 'Ya' : 'Tidak' }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.bank-accounts.edit', $account) }}" class="text-blue-600 hover:underline">Ubah</a>
                            <form method="POST" action="{{ route('admin.bank-accounts.destroy', $account) }}" class="inline" onsubmit="return confirm('Hapus rekening ini?')">
                                @csrf @method('DELETE')
                                <button class="ml-3 text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada rekening.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@extends('layouts.admin')

@section('content')
    <h1 class="mb-6 text-3xl font-bold text-gray-900">Ubah Rekening</h1>
    <form method="POST" action="{{ route('admin.bank-accounts.update', $account) }}" class="max-w-lg space-y-4 rounded-2xl border bg-white p-6 shadow-sm">
        @csrf @method('PUT')
        @include('admin.bank-accounts._fields')
        <button class="rounded-lg bg-black px-4 py-2 font-bold text-white hover:bg-gray-800">Perbarui</button>
    </form>
@endsection

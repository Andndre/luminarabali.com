@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-900 text-sm mb-4 inline-block">&larr; Kembali</a>
        <h1 class="text-3xl font-bold text-gray-900">Edit Admin</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden max-w-2xl">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="p-4 md:p-8 space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Divisi / Role</label>
                <select name="division" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500">
                    <option value="photobooth" {{ $user->division == 'photobooth' ? 'selected' : '' }}>Luminara Photobooth</option>
                    <option value="visual" {{ $user->division == 'visual' ? 'selected' : '' }}>Luminara Visual</option>
                    <option value="designer" {{ $user->division == 'designer' ? 'selected' : '' }}>Desainer (Studio &amp; template)</option>
                    <option value="super_admin" {{ $user->division == 'super_admin' ? 'selected' : '' }}>Super Admin (Akses Semua)</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    Desainer setara super admin di dalam Studio, termasuk komponen HTML mentah
                    yang ikut tersalin ke setiap undangan dari template itu. Beri hanya ke orang
                    yang kamu percaya seperti admin.
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru (Opsional)</label>
                <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" minlength="8" placeholder="Kosongkan jika tidak ingin mengubah">
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="bg-black text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:bg-gray-800 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection

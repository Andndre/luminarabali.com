@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-900 text-sm mb-4 inline-block">&larr; Kembali</a>
        <h1 class="text-3xl font-bold text-gray-900">Tambah Admin Baru</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden max-w-2xl">
        <form action="{{ route('admin.users.store') }}" method="POST" class="p-4 md:p-8 space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Divisi / Role</label>
                <select name="division" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500">
                    <option value="photobooth">Luminara Photobooth</option>
                    <option value="visual">Luminara Visual</option>
                    <option value="designer">Desainer (Studio &amp; template)</option>
                    <option value="super_admin">Super Admin (Akses Semua)</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    Admin divisi hanya bisa melihat data divisinya sendiri. Desainer setara
                    super admin di dalam Studio, termasuk komponen HTML mentah yang ikut
                    tersalin ke setiap undangan dari template itu.
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500" required minlength="8">
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="bg-black text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:bg-gray-800 transition">
                    Buat Akun
                </button>
            </div>
        </form>
    </div>
@endsection

@extends('layouts.admin')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manajemen Admin</h1>
            <p class="text-gray-500">Kelola akses admin untuk setiap divisi.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="bg-black text-white hover:bg-gray-800 font-bold py-2 px-4 rounded-lg transition">
            + Tambah Admin
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 whitespace-nowrap">Nama</th>
                        <th class="px-6 py-4 whitespace-nowrap">Email</th>
                        <th class="px-6 py-4 whitespace-nowrap">Divisi</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->division == 'super_admin')
                                    <span class="bg-purple-100 text-purple-800 text-xs font-bold px-2 py-1 rounded">Super Admin</span>
                                @elseif($user->division == 'designer')
                                    <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2 py-1 rounded">Desainer</span>
                                @elseif($user->division == 'photobooth')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded">Photobooth</span>
                                @elseif($user->division == 'visual')
                                    <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded">Visual</span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2 py-1 rounded">{{ $user->division }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right flex justify-end gap-2 whitespace-nowrap">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</a>
                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this)" class="text-red-600 hover:text-red-800 text-sm font-medium">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card List -->
        <div class="block md:hidden divide-y divide-gray-100">
            @foreach($users as $user)
                <div class="p-4 space-y-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                        <div>
                            @if($user->division == 'super_admin')
                                <span class="bg-purple-100 text-purple-800 text-[10px] font-bold px-2 py-1 rounded">Super Admin</span>
                            @elseif($user->division == 'designer')
                                <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2 py-1 rounded">Desainer</span>
                            @elseif($user->division == 'photobooth')
                                <span class="bg-yellow-100 text-yellow-800 text-[10px] font-bold px-2 py-1 rounded">Photobooth</span>
                            @elseif($user->division == 'visual')
                                <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-1 rounded">Visual</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-[10px] font-bold px-2 py-1 rounded">{{ $user->division }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2 border-t border-gray-50">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="text-blue-600 text-sm font-medium">Edit</a>
                        @if(auth()->id() !== $user->id)
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete(this)" class="text-red-600 text-sm font-medium">Hapus</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function confirmDelete(button) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Admin yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            })
        }
    </script>
@endsection

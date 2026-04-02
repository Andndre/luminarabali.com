@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.links.index') }}" class="text-gray-500 hover:text-gray-900 text-sm mb-2 inline-block">
            &larr; Kembali
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Edit Link</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden max-w-2xl">
        <div class="p-6 md:p-8">
            @include('admin.links._form', ['link' => $link])
        </div>
    </div>
@endsection

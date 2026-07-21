@extends('layouts.dashboard')

@section('title', 'Isi Undangan')

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
    @include('invitations.customizer._app', [
        'page' => $page,
        'backUrl' => route('invitations.index'),
        'loadUrl' => route('invitations.customizer.load', $page->id),
        'saveUrl' => route('invitations.customizer.save', $page->id),
        'previewUrl' => route('invitations.customizer.preview', $page->id),
        'pickerUrl' => '',
    ])
@endsection

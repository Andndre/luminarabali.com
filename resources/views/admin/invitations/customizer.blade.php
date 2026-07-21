@extends('layouts.admin')

@section('title', 'Customizer — ' . $page->title)

@section('content')
    @include('invitations.customizer._app', [
        'page' => $page,
        'backUrl' => route('admin.invitations.index'),
        'loadUrl' => route('api.admin.invitations.customizer.load', $page->id),
        'saveUrl' => route('api.admin.invitations.customizer.save', $page->id),
        'previewUrl' => route('admin.invitations.customizer-preview', $page->id),
        'pickerUrl' => route('admin.assets.index') . '?page_id=' . $page->id,
    ])
@endsection

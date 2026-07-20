@props(['props' => [], 'section' => null, 'page' => null])

{{-- Escape hatch level 3: HTML mentah dari super admin. Bypass total sistem props. --}}
{!! $props['html'] ?? '' !!}

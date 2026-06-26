@php
    $viteManifest = public_path('build/manifest.json');
    $viteHotFile = public_path('hot');
@endphp

@if (file_exists($viteManifest) || file_exists($viteHotFile))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@else
    <link rel="stylesheet" href="{{ asset('fallback.css') }}">
@endif

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    @include('partials.head', ['title' => __('landing.page_title').' - FluxFlow'])
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta Tags --}}
    <meta name="description" content="{{ __('landing.meta_description') }}">
    <meta name="keywords" content="project management, kanban board, task management, team collaboration, productivity, workflow">

    {{-- Open Graph --}}
    <meta property="og:title" content="{{ __('landing.page_title') }} - FluxFlow">
    <meta property="og:description" content="{{ __('landing.meta_description') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cairo:400,500,600,700" rel="stylesheet" />

    <style>
        html { background-color: white; }
        html.dark { background-color: #020617; }
    </style>
    @livewireStyles
</head>
<body class="font-sans antialiased" style="font-family: 'Cairo', ui-sans-serif, system-ui, sans-serif;">
    {{ $slot }}

    @fluxScripts
</body>
</html>

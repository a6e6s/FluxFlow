<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>503 - FluxFlow</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cairo:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="font-sans antialiased bg-slate-950" style="font-family: 'Cairo', ui-sans-serif, system-ui, sans-serif;">
    <div class="min-h-screen flex items-center justify-center px-6 relative overflow-hidden">
        {{-- Animated Background --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 ltr:left-10 rtl:right-10 w-32 h-32 bg-gradient-to-br from-cyan-500/20 to-blue-600/20 rounded-3xl blur-xl animate-pulse"></div>
            <div class="absolute bottom-20 ltr:right-10 rtl:left-10 w-40 h-40 bg-gradient-to-br from-indigo-500/20 to-purple-600/20 rounded-3xl blur-2xl animate-pulse" style="animation-delay: 1s;"></div>
        </div>

        {{-- Error Card --}}
        <div class="relative z-10 max-w-2xl w-full" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-3xl p-12 text-center"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0">

                {{-- Logo --}}
                <div class="mb-8 opacity-50 transform rotate-3">
                    <img src="{{ asset('logo.png') }}" alt="FluxFlow" class="w-40 mx-auto">
                </div>

                {{-- Error Code --}}
                <div class="text-8xl font-bold text-cyan-500 mb-6 animate-pulse">503</div>

                {{-- Error Message --}}
                <h1 class="text-3xl font-bold text-white mb-4">In the Workshop for a Tune-Up</h1>
                <p class="text-xl text-white/70 mb-8">FluxFlow is currently being serviced. We're sharpening the pixels and oiling the drag-and-drop.</p>

                {{-- CTA Button --}}
                <a href="{{ auth()->check() ? route('dashboard') : route('landing') }}"
                   class="inline-block px-8 py-4 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold text-lg transition-all hover:shadow-xl hover:shadow-blue-500/25 hover:scale-105">
                    Back to Flow
                </a>
            </div>
        </div>
    </div>
</body>
</html>

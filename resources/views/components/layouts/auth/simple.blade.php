<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-slate-100 dark:bg-[#101a22] antialiased">
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
        <div class="w-full max-w-md bg-[#1c2630] rounded-2xl shadow-2xl border border-[#283239] p-8">
            {{-- Logo --}}
            <div class="flex flex-col items-center gap-4 mb-8">
                <img src="{{ asset('logo.png') }}" alt="" class="flex items-center w-80">
            </div>
            {{ $slot }}
        </div>
    </div>
    @fluxScripts
</body>

</html>

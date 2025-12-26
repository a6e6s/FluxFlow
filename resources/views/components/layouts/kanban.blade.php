<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="font-sans bg-slate-100 dark:bg-[#101a22] text-slate-900 dark:text-white overflow-hidden h-screen flex flex-col">
    {{-- Header --}}
    <header class="h-16 shrink-0 flex items-center justify-between border-b border-slate-200 dark:border-slate-700/50 bg-white dark:bg-[#111518] px-6 z-20">
        <div class="flex items-center gap-8">
            {{-- Logo --}}
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 text-[#1392ec]">
                <div class="size-8 flex items-center justify-center rounded-lg bg-[#1392ec]/10">
                    <x-lucide-waves class="size-5 text-[#1392ec]" />
                </div>
                <h2 class="text-slate-900 dark:text-white text-xl font-bold tracking-tight">ZenFlow</h2>
            </a>

            {{-- Search --}}
            <label class="hidden md:flex relative group w-64 lg:w-96">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-lucide-search class="size-4 text-slate-400 group-focus-within:text-[#1392ec] transition-colors" />
                </div>
                <input
                    class="block w-full rounded-lg border-none bg-slate-100 dark:bg-[#283239] py-2 pl-10 pr-3 text-sm placeholder-slate-500 focus:ring-2 focus:ring-[#1392ec] dark:text-white transition-all"
                    placeholder="Search projects, tasks, or tags..."
                    type="text"
                />
                <div class="absolute inset-y-0 right-0 pr-2 flex items-center">
                    <span class="text-xs text-slate-500 border border-slate-600 rounded px-1.5 py-0.5">⌘K</span>
                </div>
            </label>
        </div>

        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                {{-- Notifications --}}
                <button class="flex items-center justify-center size-9 rounded-lg hover:bg-slate-100 dark:hover:bg-[#283239] text-slate-500 dark:text-slate-400 transition-colors relative">
                    <x-lucide-bell class="size-5" />
                    <span class="absolute top-2 right-2 size-2 bg-red-500 rounded-full border-2 border-white dark:border-[#111518]"></span>
                </button>

                {{-- Settings --}}
                <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center justify-center size-9 rounded-lg hover:bg-slate-100 dark:hover:bg-[#283239] text-slate-500 dark:text-slate-400 transition-colors">
                    <x-lucide-settings class="size-5" />
                </a>
            </div>

            <div class="h-6 w-px bg-slate-200 dark:bg-slate-700 mx-2"></div>

            {{-- User Dropdown --}}
            <flux:dropdown position="bottom" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                    class="cursor-pointer"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span class="flex h-full w-full items-center justify-center rounded-lg bg-[#1392ec]/20 text-[#1392ec]">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>
                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </div>
    </header>

    {{-- Main Content --}}
    <div class="flex flex-1 overflow-hidden">
        {{-- Sidebar with @persist for SPA-like navigation --}}
        @persist('sidebar')
            <livewire:project-sidebar />
        @endpersist

        {{-- Main Area --}}
        <main class="flex-1 flex flex-col min-w-0 bg-white dark:bg-[#101a22]">
            {{ $slot }}
        </main>
    </div>

    @fluxScripts
</body>
</html>

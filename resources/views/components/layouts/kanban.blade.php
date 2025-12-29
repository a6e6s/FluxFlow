<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    @include('partials.head')
</head>

<body x-data="{
    currentProjectId: null,
    init() {
        Livewire.on('project-selected', (data) => {
            this.currentProjectId = data.projectId;
        });
    },
    handleKeydown(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.isContentEditable) return;
        if (e.key === 'n' || e.key === 'N') {
            if (this.currentProjectId) {
                e.preventDefault();
                this.$dispatch('open-create-task-modal', { projectId: this.currentProjectId });
            }
        }
        if (e.key === 'p' || e.key === 'P') {
            e.preventDefault();
            this.$dispatch('open-create-project-modal');
        }
    }
}" @keydown.window="handleKeydown($event)"
    class="font-sans bg-slate-100 dark:bg-[#101a22] text-slate-900 dark:text-white overflow-hidden h-screen flex flex-col">
    {{-- Header --}}
    <header
        class="h-16 shrink-0 flex items-center justify-between border-b border-slate-200 dark:border-slate-700/50 bg-white dark:bg-[#111518] px-6 z-20">
        <div class="flex items-center gap-8">
            {{-- Logo --}}
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 text-[#1392ec]">
                <div class=" flex items-center justify-center rounded-lg bg-[#1392ec]/5">
                </div>
                <img src="{{ asset('logo.png') }}" alt="" class="flex items-center w-40">
                {{-- <h2 class="text-slate-900 dark:text-white text-xl font-bold tracking-tight">FluxFlow</h2> --}}
            </a>
        </div>

        {{-- Search - Trigger for Global Search Modal --}}
        <button @click="$dispatch('open-global-search')"
            class="hidden md:flex items-center gap-3 relative group w-64 lg:w-96 px-3 py-2 rounded-lg bg-slate-100 dark:bg-[#283239] hover:bg-slate-200 dark:hover:bg-[#323d46] transition-colors cursor-pointer">
            <x-lucide-search class="size-4 text-slate-400 group-hover:text-[#1392ec] transition-colors" />
            <span class="text-sm text-slate-500">{{ __('app.search_placeholder') }}</span>
            <span class="ml-auto text-xs text-slate-500 border border-slate-600 rounded px-1.5 py-0.5">⌘K</span>
        </button>

        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                {{-- Language Switcher --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="flex items-center justify-center size-9 rounded-lg hover:bg-slate-100 dark:hover:bg-[#283239] text-slate-500 dark:text-slate-400 transition-colors"
                        title="{{ __('app.language') }}">
                        <x-lucide-languages class="size-5" />
                    </button>
                    <div x-show="open" x-transition @click.away="open = false"
                        class="absolute right-0 top-full mt-2 w-36 bg-white dark:bg-[#1c2630] border border-slate-200 dark:border-[#283239] rounded-lg shadow-xl z-50 py-1">
                        <a href="{{ route('language.switch', 'en') }}" @class([
                            'flex items-center gap-2 px-3 py-2 text-sm transition-colors',
                            'text-[#1392ec] bg-[#1392ec]/10' => app()->getLocale() === 'en',
                            'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#283239]' =>
                                app()->getLocale() !== 'en',
                        ])>
                            <span class="text-base">🇺🇸</span>
                            English
                        </a>
                        <a href="{{ route('language.switch', 'ar') }}" @class([
                            'flex items-center gap-2 px-3 py-2 text-sm transition-colors',
                            'text-[#1392ec] bg-[#1392ec]/10' => app()->getLocale() === 'ar',
                            'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#283239]' =>
                                app()->getLocale() !== 'ar',
                        ])>
                            <span class="text-base">🇸🇦</span>
                            العربية
                        </a>
                    </div>
                </div>

                {{-- Notifications --}}
                <button
                    class="flex items-center justify-center size-9 rounded-lg hover:bg-slate-100 dark:hover:bg-[#283239] text-slate-500 dark:text-slate-400 transition-colors relative">
                    <x-lucide-bell class="size-5" />
                    <span
                        class="absolute top-2 right-2 size-2 bg-red-500 rounded-full border-2 border-white dark:border-[#111518]"></span>
                </button>

                {{-- Settings --}}
                <button @click="$dispatch('open-user-profile-modal')"
                    class="flex items-center justify-center size-9 rounded-lg hover:bg-slate-100 dark:hover:bg-[#283239] text-slate-500 dark:text-slate-400 transition-colors"
                    title="{{ __('Profile Settings') }}">
                    <x-lucide-settings class="size-5" />
                </button>
            </div>

            <div class="h-6 w-px bg-slate-200 dark:bg-slate-700 mx-2"></div>

            {{-- User Dropdown --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-[#283239] transition-colors">
                    <div
                        class="size-8 flex items-center justify-center rounded-lg bg-[#1392ec]/20 text-[#1392ec] text-sm font-semibold">
                        {{ auth()->user()->initials() }}
                    </div>
                    <x-lucide-chevron-down class="size-4 text-slate-400" />
                </button>

                <div x-show="open" x-transition @click.away="open = false"
                    class="absolute right-0 top-full mt-2 w-64 bg-[#1c2630] border border-[#283239] rounded-lg shadow-xl z-50 py-2">
                    {{-- User Info --}}
                    <div class="flex items-center gap-3 px-4 py-3 border-b border-[#283239]">
                        <div
                            class="size-10 flex items-center justify-center rounded-lg bg-[#1392ec]/20 text-[#1392ec] font-semibold">
                            {{ auth()->user()->initials() }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white font-medium truncate">{{ auth()->user()->name }}</p>
                            <p class="text-slate-400 text-sm truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>

                    {{-- Menu Items --}}
                    <div class="py-1">
                        <button @click="$dispatch('open-user-profile-modal'); open = false"
                            class="flex items-center gap-3 w-full px-4 py-2 text-sm text-slate-300 hover:text-white hover:bg-[#283239] transition-colors">
                            <x-lucide-settings class="size-4" />
                            {{ __('Settings') }}
                        </button>

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-3 w-full px-4 py-2 text-sm text-slate-300 hover:text-white hover:bg-[#283239] transition-colors">
                                <x-lucide-log-out class="size-4" />
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <div class="flex flex-1 min-h-0 overflow-hidden">
        {{-- Sidebar with @persist for SPA-like navigation --}}
        @persist('sidebar')
            <livewire:project-sidebar />
        @endpersist

        {{-- Main Area --}}
        <main class="flex-1 flex flex-col min-w-0 bg-white dark:bg-[#101a22]">
            {{ $slot }}
        </main>
    </div>

    {{-- Modals & Slide-overs --}}
    <livewire:task-details />
    <livewire:global-search />
    <livewire:create-project-modal />
    <livewire:edit-project-modal />
    <livewire:create-task-modal />
    <livewire:user-profile-modal />

    @fluxScripts
</body>

</html>

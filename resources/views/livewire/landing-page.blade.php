<div class="min-h-screen bg-white dark:bg-slate-950 relative overflow-hidden transition-colors duration-300">
    <style>
        @keyframes float-slow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            25% { transform: translateY(-20px) rotate(5deg); }
            50% { transform: translateY(-10px) rotate(10deg); }
            75% { transform: translateY(-30px) rotate(5deg); }
        }
        @keyframes float-medium {
            0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); }
            33% { transform: translateY(-25px) rotate(-8deg) scale(1.05); }
            66% { transform: translateY(-15px) rotate(8deg) scale(0.95); }
        }
        @keyframes float-fast {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            20% { transform: translateY(-15px) rotate(3deg); }
            40% { transform: translateY(-25px) rotate(-3deg); }
            60% { transform: translateY(-10px) rotate(6deg); }
            80% { transform: translateY(-20px) rotate(-6deg); }
        }
        @keyframes float-reverse {
            0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); }
            25% { transform: translateY(20px) rotate(-5deg) scale(1.1); }
            50% { transform: translateY(10px) rotate(-10deg) scale(0.9); }
            75% { transform: translateY(30px) rotate(-5deg) scale(1.05); }
        }
        .animate-float-slow { animation: float-slow 8s ease-in-out infinite; }
        .animate-float-medium { animation: float-medium 6s ease-in-out infinite; }
        .animate-float-fast { animation: float-fast 4s ease-in-out infinite; }
        .animate-float-reverse { animation: float-reverse 7s ease-in-out infinite; }
    </style>

    {{-- Animated Background Elements --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 ltr:left-10 rtl:right-10 w-32 h-32 bg-gradient-to-br from-blue-400/30 dark:from-blue-500/40 to-purple-500/30 dark:to-purple-600/40 rounded-3xl blur-xl animate-float-slow transform rotate-12"></div>
        <div class="absolute top-40 ltr:right-20 rtl:left-20 w-24 h-24 bg-gradient-to-br from-emerald-400/40 dark:from-emerald-500/30 to-teal-500/40 dark:to-teal-600/30 rounded-full blur-2xl animate-float-medium transform -rotate-45"></div>
        <div class="absolute bottom-32 ltr:left-1/4 rtl:right-1/4 w-40 h-40 bg-gradient-to-br from-indigo-400/25 dark:from-indigo-500/35 to-blue-500/25 dark:to-blue-600/35 rounded-2xl blur-3xl animate-float-fast transform rotate-45"></div>
        <div class="absolute top-1/3 ltr:right-1/4 rtl:left-1/4 w-28 h-28 bg-gradient-to-br from-purple-400/35 dark:from-purple-500/45 to-pink-500/35 dark:to-pink-600/45 rounded-full blur-xl animate-float-reverse transform -rotate-12"></div>
        <div class="absolute bottom-20 ltr:right-10 rtl:left-10 w-36 h-36 bg-gradient-to-br from-cyan-400/25 dark:from-cyan-500/35 to-blue-500/25 dark:to-blue-600/35 rounded-3xl blur-2xl animate-float-slow transform rotate-90"></div>
        <div class="absolute top-60 ltr:left-1/3 rtl:right-1/3 w-20 h-20 bg-gradient-to-br from-yellow-400/40 dark:from-yellow-500/30 to-orange-500/40 dark:to-orange-600/30 rounded-full blur-xl animate-float-medium transform rotate-180"></div>
    </div>

    {{-- Navigation --}}
    <nav class="relative z-10 px-6 py-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('logo.png') }}" alt="FluxFlow" class="w-32">
            </div>
            <div class="flex items-center gap-4">
                {{-- Theme Toggle --}}
                <button type="button" x-data x-on:click="$flux.dark = ! $flux.dark"
                        class="flex items-center justify-center size-9 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-700 transition-all duration-200">
                    <x-lucide-sun class="size-5" x-show="$flux.dark" />
                    <x-lucide-moon class="size-5" x-show="! $flux.dark" />
                </button>

                {{-- Language Switcher --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-700 transition-all duration-200">
                        <x-lucide-languages class="size-4" />
                        <span class="text-sm">{{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}</span>
                    </button>
                    <div x-show="open" x-transition @click.away="open = false" class="absolute ltr:right-0 rtl:left-0 top-full mt-2 w-36 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-xl z-[100] py-1">
                        <button wire:click="switchLanguage('en')" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors w-full text-left">
                            <span>🇺🇸</span> English
                        </button>
                        <button wire:click="switchLanguage('ar')" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors w-full text-left">
                            <span>🇸🇦</span> العربية
                        </button>
                    </div>
                </div>
                <button wire:click="openSignup" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-all hover:shadow-lg hover:shadow-blue-500/25">
                    {{ __('landing.get_started') }}
                </button>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="relative z-1 px-6 py-20">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="text-center ">
                    <h1 class="text-4xl lg:text-5xl font-bold mb-6 leading-normal">
                        <span class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-700 dark:from-white dark:via-white dark:to-white/80 bg-clip-text text-transparent">
                            {{ __('landing.hero_title') }}
                        </span>
                    </h1>
                    <p class="text-xl text-slate-600 dark:text-white/70 mb-8 leading-relaxed">
                        {{ __('landing.hero_subtitle') }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center ">
                        <button wire:click="openSignup" class="px-8 py-4 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold text-lg transition-all hover:shadow-xl hover:shadow-blue-500/25 hover:scale-105">
                            {{ __('landing.get_started_free') }}
                        </button>
                        <a href="{{ route('login') }}" class="px-8 py-4 bg-slate-100 dark:bg-white/10 backdrop-blur-md border border-slate-200 dark:border-white/20 text-slate-700 dark:text-white rounded-lg font-semibold text-lg hover:bg-slate-200 dark:hover:bg-white/20 transition-all text-center">
                            {{ __('landing.sign_in') }}
                        </a>
                    </div>
                </div>

                {{-- Product Mockup --}}
                <div class="relative">
                    <div class="bg-slate-50 dark:bg-white/5 backdrop-blur-md border border-slate-200 dark:border-white/10 rounded-2xl p-4 lg:p-6 shadow-2xl">
                        {{-- Mock Sidebar --}}
                        <div class="flex flex-col lg:flex-row gap-4 mb-4">
                            <div class="w-full lg:w-64 bg-white dark:bg-white/5 rounded-lg p-4 border border-slate-300 dark:border-white/10 shadow-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="text-slate-500 dark:text-white/60 text-xs uppercase tracking-wider">{{ __('app.workspace') }}</div>
                                    <div class="w-6 h-6 bg-blue-500 rounded-lg flex items-center justify-center">
                                        <x-lucide-plus class="size-4 text-white" />
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    @for($i = 0; $i < 3; $i++)
                                        <div class="flex items-center gap-3 p-2 rounded-lg {{ $i === 0 ? 'bg-blue-500/20 border border-blue-500/30' : 'bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10' }}">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                                <span class="text-xs text-white">{{ chr(65 + $i) }}</span>
                                            </div>
                                            <div class="flex-1">
                                                <div class="text-slate-700 dark:text-white/80 text-sm">{{ __('landing.project') }} {{ $i + 1 }}</div>
                                                <div class="text-blue-400 text-xs">{{ rand(60, 95) }}% {{ __('app.complete') }}</div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            {{-- Mock Kanban --}}
                            <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                @foreach(['todo', 'doing', 'done'] as $status)
                                    <div class="bg-white dark:bg-white/5 rounded-lg p-3 border border-slate-300 dark:border-white/10 shadow-sm">
                                        <div class="text-slate-500 dark:text-white/60 text-xs uppercase tracking-wider mb-3">{{ __('app.' . $status) }}</div>
                                        <div class="space-y-2">
                                            @for($i = 0; $i < ($status === 'done' ? 1 : 2); $i++)
                                                <div class="bg-slate-50 dark:bg-white/10 rounded-lg p-3 border border-slate-200 dark:border-white/20 shadow-sm">
                                                    <div class="text-slate-700 dark:text-white/80 text-sm mb-1">{{ __('landing.task_example') }} {{ $i + 1 }}</div>
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-2 h-2 rounded-full {{ $status === 'todo' ? 'bg-slate-400' : ($status === 'doing' ? 'bg-yellow-400' : 'bg-blue-400') }}"></div>
                                                        <span class="text-xs text-slate-500 dark:text-white/60">{{ __('app.' . ['low', 'medium', 'high'][rand(0, 2)]) }}</span>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section class="relative z-10 px-6 py-20">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-slate-900 dark:text-white mb-4">{{ __('landing.features_title') }}</h2>
                <p class="text-xl text-slate-600 dark:text-white/70">{{ __('landing.features_subtitle') }}</p>
            </div>

            <div class="grid md:grid-cols-4 gap-8">
                {{-- Feature 1 --}}
                <div class="bg-slate-50 dark:bg-white/5 backdrop-blur-md border border-slate-200 dark:border-white/10 rounded-2xl p-8 hover:bg-white dark:hover:bg-white/10 transition-all shadow-sm">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mb-6">
                        <x-lucide-layers class="size-6 text-blue-500 dark:text-blue-400" />
                    </div>
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">{{ __('landing.feature_1_title') }}</h3>
                    <p class="text-slate-600 dark:text-white/70">{{ __('landing.feature_1_desc') }}</p>
                </div>

                {{-- Feature 2 --}}
                <div class="bg-slate-50 dark:bg-white/5 backdrop-blur-md border border-slate-200 dark:border-white/10 rounded-2xl p-8 hover:bg-white dark:hover:bg-white/10 transition-all shadow-sm">
                    <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center mb-6">
                        <x-lucide-move class="size-6 text-purple-500 dark:text-purple-400" />
                    </div>
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">{{ __('landing.feature_2_title') }}</h3>
                    <p class="text-slate-600 dark:text-white/70">{{ __('landing.feature_2_desc') }}</p>
                </div>

                {{-- Feature 3 --}}
                <div class="bg-slate-50 dark:bg-white/5 backdrop-blur-md border border-slate-200 dark:border-white/10 rounded-2xl p-8 hover:bg-white dark:hover:bg-white/10 transition-all shadow-sm">
                    <div class="w-12 h-12 bg-emerald-500/20 rounded-lg flex items-center justify-center mb-6">
                        <x-lucide-shield-check class="size-6 text-emerald-500 dark:text-emerald-400" />
                    </div>
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">{{ __('landing.feature_3_title') }}</h3>
                    <p class="text-slate-600 dark:text-white/70">{{ __('landing.feature_3_desc') }}</p>
                </div>

                {{-- Feature 4 --}}
                <div class="bg-slate-50 dark:bg-white/5 backdrop-blur-md border border-slate-200 dark:border-white/10 rounded-2xl p-8 hover:bg-white dark:hover:bg-white/10 transition-all shadow-sm">
                    <div class="w-12 h-12 bg-indigo-500/20 rounded-lg flex items-center justify-center mb-6">
                        <x-lucide-search class="size-6 text-indigo-500 dark:text-indigo-400" />
                    </div>
                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">{{ __('landing.feature_4_title') }}</h3>
                    <p class="text-slate-600 dark:text-white/70">{{ __('landing.feature_4_desc') }}</p>
                </div>
            </div>

            {{-- Additional Features Grid --}}
            <div class="mt-16">
                <div class="mb-8 text-center">
                    <h3 class="text-2xl font-semibold text-slate-900 dark:text-white mb-3">{{ __('landing.updates_title') }}</h3>
                    <p class="text-slate-600 dark:text-white/70">{{ __('landing.updates_desc') }}</p>
                </div>

                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                    {{-- Compact Sidebar --}}
                    <div class="bg-white/80 dark:bg-white/5 backdrop-blur-md border border-slate-200 dark:border-white/10 rounded-2xl p-8 hover:bg-white dark:hover:bg-white/10 transition-all shadow-sm">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mb-6">
                            <x-lucide-panel-left class="size-6 text-blue-500 dark:text-blue-400" />
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">{{ __('landing.update_sidebar_title') }}</h3>
                        <p class="text-slate-600 dark:text-white/70">{{ __('landing.update_sidebar_desc') }}</p>
                    </div>

                    {{-- Theme Persistence --}}
                    <div class="bg-white/80 dark:bg-white/5 backdrop-blur-md border border-slate-200 dark:border-white/10 rounded-2xl p-8 hover:bg-white dark:hover:bg-white/10 transition-all shadow-sm">
                        <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center mb-6">
                            <x-lucide-sparkles class="size-6 text-purple-500 dark:text-purple-400" />
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">{{ __('landing.update_theme_title') }}</h3>
                        <p class="text-slate-600 dark:text-white/70">{{ __('landing.update_theme_desc') }}</p>
                    </div>

                    {{-- API Access --}}
                    <div class="bg-white/80 dark:bg-white/5 backdrop-blur-md border border-slate-200 dark:border-white/10 rounded-2xl p-8 hover:bg-white dark:hover:bg-white/10 transition-all shadow-sm">
                        <div class="w-12 h-12 bg-emerald-500/20 rounded-lg flex items-center justify-center mb-6">
                            <x-lucide-key class="size-6 text-emerald-500 dark:text-emerald-400" />
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">{{ __('landing.update_api_title') }}</h3>
                        <p class="text-slate-600 dark:text-white/70">{{ __('landing.update_api_desc') }}</p>
                    </div>

                    {{-- Keyboard Shortcuts --}}
                    <div class="bg-white/80 dark:bg-white/5 backdrop-blur-md border border-slate-200 dark:border-white/10 rounded-2xl p-8 hover:bg-white dark:hover:bg-white/10 transition-all shadow-sm">
                        <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center mb-6">
                            <x-lucide-keyboard class="size-6 text-yellow-400" />
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-4">{{ __('landing.shortcuts_title') }}</h3>
                        <p class="text-slate-600 dark:text-white/70 mb-4">{{ __('landing.shortcuts_desc') }}</p>
                        <div class="space-y-2">
                            <div class="flex items-center gap-3">
                                <kbd class="px-2 py-1 bg-slate-200 dark:bg-white/10 rounded text-xs text-slate-700 dark:text-white/80">N</kbd>
                                <span class="text-sm text-slate-500 dark:text-white/60">{{ __('landing.shortcut_new_task') }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <kbd class="px-2 py-1 bg-slate-200 dark:bg-white/10 rounded text-xs text-slate-700 dark:text-white/80">P</kbd>
                                <span class="text-sm text-slate-500 dark:text-white/60">{{ __('landing.shortcut_new_project') }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <kbd class="px-2 py-1 bg-slate-200 dark:bg-white/10 rounded text-xs text-slate-700 dark:text-white/80">⌘K</kbd>
                                <span class="text-sm text-slate-500 dark:text-white/60">{{ __('landing.shortcut_search') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats Section --}}
    <section class="relative z-10 px-6 py-20">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white/80 dark:bg-white/5 backdrop-blur-md border border-slate-200 dark:border-white/10 rounded-2xl p-12">
                <div class="grid md:grid-cols-3 gap-8 text-center">
                    <div>
                        <div class="text-3xl font-bold text-emerald-500 dark:text-emerald-400 mb-2">{{ __('landing.stat_1_number') }}</div>
                        <div class="text-slate-600 dark:text-white/70">{{ __('landing.stat_1_label') }}</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-blue-500 dark:text-blue-400 mb-2">{{ __('landing.stat_2_number') }}</div>
                        <div class="text-slate-600 dark:text-white/70">{{ __('landing.stat_2_label') }}</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-purple-500 dark:text-purple-400 mb-2">{{ __('landing.stat_3_number') }}</div>
                        <div class="text-slate-600 dark:text-white/70">{{ __('landing.stat_3_label') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="relative z-10 px-6 py-20">
        <div class="max-w-4xl mx-auto text-center">
            <div class="bg-gradient-to-r from-blue-500/10 to-purple-500/10 dark:from-emerald-500/10 dark:to-blue-500/10 backdrop-blur-md border border-slate-200 dark:border-white/20 rounded-3xl p-12">
                <h2 class="text-4xl font-bold text-slate-900 dark:text-white mb-6">{{ __('landing.cta_title') }}</h2>
                <p class="text-xl text-slate-600 dark:text-white/70 mb-8">{{ __('landing.cta_subtitle') }}</p>
                <button wire:click="openSignup" class="px-12 py-4 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold text-xl transition-all hover:shadow-xl hover:shadow-blue-500/25 hover:scale-105">
                    {{ __('landing.start_free_trial') }}
                </button>
            </div>
        </div>
    </section>

    {{-- Signup Modal --}}
    @if($showSignup)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="closeSignup">
            <div class="bg-white/90 dark:bg-white/10 backdrop-blur-md border border-slate-200 dark:border-white/20 rounded-2xl p-8 w-full max-w-md">
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-6 text-center">{{ __('landing.create_account') }}</h3>
                <form wire:submit="register" class="space-y-4">
                    <div>
                        <input type="text" wire:model="name" placeholder="{{ __('landing.full_name') }}" class="w-full px-4 py-3 bg-white dark:bg-white/10 border border-slate-200 dark:border-white/20 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <input type="email" wire:model="email" placeholder="{{ __('landing.email') }}" class="w-full px-4 py-3 bg-white dark:bg-white/10 border border-slate-200 dark:border-white/20 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <input type="password" wire:model="password" placeholder="{{ __('landing.password') }}" class="w-full px-4 py-3 bg-white dark:bg-white/10 border border-slate-200 dark:border-white/20 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="w-full px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold transition-all hover:shadow-lg hover:shadow-blue-500/25" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ __('landing.create_account') }}</span>
                        <span wire:loading>{{ __('landing.creating_account') }}</span>
                    </button>
                </form>
                <div class="mt-6 text-center">
                    <p class="text-slate-600 dark:text-white/60 text-sm">{{ __('landing.have_account') }}
                        <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300">{{ __('landing.sign_in') }}</a>
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>

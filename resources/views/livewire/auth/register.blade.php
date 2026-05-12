<x-layouts.auth>
    <div class="space-y-6">
        <div class="text-center">
            <h2 class="text-xl font-semibold text-white mb-2">{{ __('Create your account') }}</h2>
            <p class="text-slate-400 text-sm">{{ __('Enter your details to get started') }}</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-slate-400 mb-2">{{ __('Name') }}</label>
                <input
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="{{ __('Full name') }}"
                    class="w-full px-4 py-3 bg-[#101a22] border border-[#283239] rounded-lg text-white placeholder-slate-500 focus:border-[#1392ec] focus:ring-1 focus:ring-[#1392ec] transition-colors"
                />
                @error('name')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address -->
            <div>
                <label class="block text-sm font-medium text-slate-400 mb-2">{{ __('Email address') }}</label>
                <input
                    name="email"
                    type="email"
                    value="{{ old('email', request('email')) }}"
                    required
                    autocomplete="email"
                    placeholder="email@example.com"
                    class="w-full px-4 py-3 bg-[#101a22] border border-[#283239] rounded-lg text-white placeholder-slate-500 focus:border-[#1392ec] focus:ring-1 focus:ring-[#1392ec] transition-colors"
                />
                @error('email')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-slate-400 mb-2">{{ __('Password') }}</label>
                <input
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="{{ __('Password') }}"
                    class="w-full px-4 py-3 bg-[#101a22] border border-[#283239] rounded-lg text-white placeholder-slate-500 focus:border-[#1392ec] focus:ring-1 focus:ring-[#1392ec] transition-colors"
                />
                @error('password')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-sm font-medium text-slate-400 mb-2">{{ __('Confirm password') }}</label>
                <input
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="{{ __('Confirm password') }}"
                    class="w-full px-4 py-3 bg-[#101a22] border border-[#283239] rounded-lg text-white placeholder-slate-500 focus:border-[#1392ec] focus:ring-1 focus:ring-[#1392ec] transition-colors"
                />
                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full px-6 py-3 bg-[#1392ec] hover:bg-blue-600 text-white font-medium rounded-lg transition-colors shadow-lg shadow-blue-500/20"
            >
                {{ __('Create account') }}
            </button>
        </form>

        <div class="text-center text-sm text-slate-400">
            <span>{{ __('Already have an account?') }}</span>
            <a href="{{ route('login') }}" class="text-[#1392ec] hover:text-blue-400 transition-colors ml-1">
                {{ __('Log in') }}
            </a>
        </div>
    </div>
</x-layouts.auth>

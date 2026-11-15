<div class="flex items-center gap-1 px-2 pb-2" role="group" aria-label="{{ __('app.language') }}">
    @foreach (['en' => 'English', 'ar' => 'العربية'] as $locale => $label)
        <a
            href="{{ route('language.switch', $locale) }}"
            @class([
                'flex-1 rounded-lg px-3 py-1.5 text-center text-sm font-medium transition-colors',
                'bg-[#1392ec]/10 text-[#1392ec]' => app()->getLocale() === $locale,
                'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5' => app()->getLocale() !== $locale,
            ])
            lang="{{ $locale }}"
            @if (app()->getLocale() === $locale) aria-current="true" @endif
        >
            {{ $label }}
        </a>
    @endforeach
</div>

<x-home-card :title="__('home.widgets.invitations')" icon="mail">
    @forelse ($invitations as $invitation)
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 py-2.5 last:border-0 dark:border-[#283239]">
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-slate-900 dark:text-white">{{ $invitation->task?->title }}</p>
                <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ __('home.labels.invited_by', ['name' => $invitation->inviter?->name]) }}</p>
            </div>
            <a href="{{ route('invitations.show', $invitation->token) }}"
                class="shrink-0 rounded-lg bg-[#1392ec] px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-600">
                {{ __('home.labels.review_invitation') }}
            </a>
        </div>
    @empty
        <x-home-empty icon="mail" :title="__('home.states.invitations_title')">
            {{ __('home.states.invitations_text') }}
        </x-home-empty>
    @endforelse
</x-home-card>

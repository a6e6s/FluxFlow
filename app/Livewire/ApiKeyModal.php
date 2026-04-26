<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ApiKeyModal extends Component
{
    public bool $open = false;

    public string $apiKey = '';

    public bool $hasApiKey = false;

    public string $generatedAtLabel = '';

    public function mount(): void
    {
        $this->syncFromUser();
    }

    #[On('open-api-key-modal')]
    public function openModal(): void
    {
        $this->syncFromUser();
        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function generate(): void
    {
        $this->currentUser()->generateApiKey();

        $this->syncFromUser();
        $this->open = true;
    }

    public function regenerate(): void
    {
        $this->generate();
    }

    public function render(): View
    {
        return view('livewire.api-key-modal');
    }

    private function syncFromUser(): void
    {
        $user = $this->currentUser()->refresh();

        $this->apiKey = $user->api_key ?? '';
        $this->hasApiKey = $user->hasApiKey();
        $this->generatedAtLabel = $user->api_key_generated_at?->diffForHumans() ?? '';
    }

    private function currentUser(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}

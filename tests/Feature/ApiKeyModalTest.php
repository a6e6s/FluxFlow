<?php

use App\Livewire\ApiKeyModal;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('user can generate an api key from the modal', function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    Livewire::test(ApiKeyModal::class)
        ->call('openModal')
        ->call('generate')
        ->assertSet('open', true)
        ->assertSet('hasApiKey', true);

    $user->refresh();

    expect($user->api_key)
        ->not->toBe('')
        ->and($user->api_key_hash)->toBe(hash('sha256', $user->api_key))
        ->and($user->api_key_generated_at)->not->toBeNull();
});

test('user can regenerate an api key from the modal', function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    $originalApiKey = $user->generateApiKey();

    Livewire::test(ApiKeyModal::class)
        ->call('openModal')
        ->call('regenerate')
        ->assertSet('open', true)
        ->assertSet('hasApiKey', true);

    $user->refresh();

    expect($user->api_key)
        ->not->toBe($originalApiKey)
        ->and($user->api_key_hash)->toBe(hash('sha256', $user->api_key))
        ->and($user->api_key_generated_at)->not->toBeNull();
});

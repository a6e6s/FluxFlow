<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('dashboard layout does not hardcode dark mode on reload', function (): void {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    get(route('dashboard'))
        ->assertOk()
        ->assertSee('window.FluxFlowTheme')
        ->assertSee('window.Flux.appearance')
        ->assertDontSee('localStorage.getItem(\'theme\')')
        ->assertDontSee('<html class="dark"');
});

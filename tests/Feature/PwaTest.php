<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('dashboard links the web app manifest and registers the service worker', function (): void {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    get(route('dashboard'))
        ->assertOk()
        ->assertSee('<link rel="manifest" href="/manifest.webmanifest">', false)
        ->assertSee("navigator.serviceWorker.register('/sw.js')", false);
});

test('web app manifest is served with installable icons', function (): void {
    $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true, 512, JSON_THROW_ON_ERROR);

    expect($manifest['display'])->toBe('standalone')
        ->and($manifest['start_url'])->toBe('/dashboard');

    $sizes = collect($manifest['icons'])->pluck('sizes')->all();

    expect($sizes)->toContain('192x192', '512x512');

    foreach ($manifest['icons'] as $icon) {
        expect(file_exists(public_path(ltrim($icon['src'], '/'))))->toBeTrue();
    }
});

test('service worker precaches the offline fallback page', function (): void {
    expect(file_get_contents(public_path('sw.js')))
        ->toContain("'/offline.html'")
        ->and(file_exists(public_path('offline.html')))->toBeTrue();
});

test('dashboard renders the mobile bottom navigation and mobile column switcher', function (): void {
    /** @var User $user */
    $user = User::factory()->create();

    $project = Project::factory()->for($user)->create();

    actingAs($user);

    get(route('projects.show', $project))
        ->assertOk()
        ->assertSee('data-mobile-nav', false)
        ->assertSee(__('home.nav'))
        ->assertSee(__('app.projects'))
        ->assertSee(__('app.search'))
        ->assertSee(__('app.profile'))
        ->assertSee('data-board-column="todo"', false);
});

test('dashboard renders the install prompt that listens for beforeinstallprompt', function (): void {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    get(route('dashboard'))
        ->assertOk()
        ->assertSee('x-on:beforeinstallprompt.window', false)
        ->assertSee(__('app.install_title'));
});

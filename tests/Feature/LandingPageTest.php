<?php

declare(strict_types=1);

use function Pest\Laravel\get;

test('landing page highlights the latest product updates', function (): void {
    get('/')
        ->assertOk()
        ->assertSee('Product Updates')
        ->assertSee('Compact Sidebar')
        ->assertSee('Theme Persistence')
        ->assertSee('API Access');
});

test('landing page uses flux appearance instead of legacy dark mode storage', function (): void {
    get('/')
        ->assertOk()
        ->assertSee('window.FluxFlowTheme =')
        ->assertSee('window.FluxFlowTheme.toggle()')
        ->assertSee('flux-theme-changed')
        ->assertDontSee("localStorage.getItem('darkMode')");
});

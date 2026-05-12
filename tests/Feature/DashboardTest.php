<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('guests are redirected to the login page', function () {
    get('/dashboard')->assertRedirect('/login');
});

test('authenticated users can visit the dashboard (kanban board)', function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    get('/dashboard')->assertOk();
});

test('authenticated users are redirected from the landing page to the dashboard', function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    get('/')->assertRedirect('/dashboard');
});

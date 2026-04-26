<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('guests are redirected to the login page', function () {
    get('/')->assertRedirect('/login');
});

test('authenticated users can visit the dashboard (kanban board)', function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    get('/')->assertStatus(200);
});

test('dashboard route redirects to root', function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    get('/dashboard')->assertRedirect('/');
});

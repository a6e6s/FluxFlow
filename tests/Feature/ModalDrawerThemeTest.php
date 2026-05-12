<?php

declare(strict_types=1);

use App\Livewire\ApiKeyModal;
use App\Livewire\CreateProjectModal;
use App\Livewire\CreateTaskModal;
use App\Livewire\EditProjectModal;
use App\Livewire\GlobalSearch;
use App\Livewire\TaskDetails;
use App\Livewire\UserProfileModal;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('modal and drawer shells support light mode surfaces', function (string $componentClass, string $expectedSnippet): void {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    Livewire::test($componentClass)
        ->assertSee($expectedSnippet, false);
})->with([
    'create project modal' => [
        CreateProjectModal::class,
        'rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-[#283239] dark:bg-[#1c2630]',
    ],
    'create task modal' => [
        CreateTaskModal::class,
        'relative w-full max-w-lg rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-[#283239] dark:bg-[#1c2630]',
    ],
    'edit project modal' => [
        EditProjectModal::class,
        'rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-[#283239] dark:bg-[#1c2630]',
    ],
    'user profile modal' => [
        UserProfileModal::class,
        'rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-[#283239] dark:bg-[#1c2630]',
    ],
    'api key modal' => [
        ApiKeyModal::class,
        'rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-[#283239] dark:bg-[#1c2630]',
    ],
    'global search modal' => [
        GlobalSearch::class,
        'rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-[#283239] dark:bg-[#1c2630]',
    ],
    'task details drawer' => [
        TaskDetails::class,
        'ltr:border-l rtl:border-r border-slate-200 bg-white shadow-2xl dark:border-[#283239] dark:bg-[#101a22]',
    ],
]);

<?php

use App\Enums\TaskStatus;
use App\Livewire\Home\DueSoon;
use App\Livewire\Home\EffortLoad;
use App\Livewire\Home\MyTasks;
use App\Livewire\Home\PendingInvitations;
use App\Livewire\Home\ProjectProgress;
use App\Livewire\Home\RecentActivity;
use App\Livewire\Home\SummaryStats;
use App\Livewire\Home\UpcomingDeadlines;
use App\Livewire\HomePage;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskInvitation;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('guests are redirected from the home page', function () {
    get('/home')->assertRedirect('/login');
});

test('home page renders for authenticated users', function () {
    $user = User::factory()->create(['name' => 'Layla Test']);

    actingAs($user);

    get('/home')->assertOk()->assertSee('Layla Test');
});

test('project progress only lists projects the user can see', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Project::factory()->create(['user_id' => $user->id, 'title' => 'Mine Project']);
    Project::factory()->create(['user_id' => $otherUser->id, 'title' => 'Foreign Project']);

    actingAs($user);

    Livewire::test(ProjectProgress::class)
        ->assertSee('Mine Project')
        ->assertDontSee('Foreign Project');
});

test('due soon widget shows overdue tasks in the user\'s projects and skips completed ones', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    Task::factory()->overdue()->create(['project_id' => $project->id, 'title' => 'Late Task']);
    Task::factory()->create([
        'project_id' => $project->id,
        'title' => 'Finished Task',
        'status' => TaskStatus::Done,
        'due_date' => today()->subDay(),
    ]);

    actingAs($user);

    Livewire::test(DueSoon::class)
        ->assertSee('Late Task')
        ->assertDontSee('Finished Task');
});

test('pending invitations only show invitations addressed to the user', function () {
    $user = User::factory()->create(['email' => 'invitee@example.com']);
    $inviter = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $inviter->id]);
    $task = Task::factory()->create(['project_id' => $project->id, 'title' => 'Shared Task']);

    $ownInvitation = TaskInvitation::factory()->create([
        'task_id' => $task->id,
        'invited_by_id' => $inviter->id,
        'email' => 'invitee@example.com',
    ]);
    $foreignInvitation = TaskInvitation::factory()->create([
        'task_id' => $task->id,
        'invited_by_id' => $inviter->id,
        'email' => 'someone-else@example.com',
    ]);

    actingAs($user);

    Livewire::test(PendingInvitations::class)
        ->assertSee('Shared Task')
        ->assertSeeHtml($ownInvitation->token)
        ->assertDontSeeHtml($foreignInvitation->token);
});

test('every home widget renders with the user\'s data', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    Task::factory()->create([
        'project_id' => $project->id,
        'assigned_to' => $user->id,
        'status' => TaskStatus::Doing,
        'effort_score' => 5,
        'due_date' => today()->addDays(2),
    ]);
    Task::factory()->create(['project_id' => $project->id, 'status' => TaskStatus::Done]);

    actingAs($user);

    $range = ['from' => today()->startOfMonth()->toDateString(), 'to' => today()->endOfMonth()->toDateString()];

    Livewire::test(SummaryStats::class, $range)->assertOk();
    Livewire::test(EffortLoad::class, $range)->assertOk();

    foreach ([UpcomingDeadlines::class, MyTasks::class, RecentActivity::class] as $widget) {
        Livewire::test($widget)->assertOk();
    }
});

test('summary stats count only tasks due inside the selected period', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    Task::factory()->create(['project_id' => $project->id, 'status' => TaskStatus::Done, 'due_date' => today()]);
    Task::factory()->create(['project_id' => $project->id, 'status' => TaskStatus::Done, 'due_date' => today()->subMonthNoOverflow()]);

    actingAs($user);

    $range = ['from' => today()->startOfMonth()->toDateString(), 'to' => today()->endOfMonth()->toDateString()];
    $metrics = Livewire::test(SummaryStats::class, $range)->instance()->metrics();

    expect(collect($metrics)->firstWhere('label', __('home.stats.done'))['value'])->toBe(1);
});

test('home period resolves last month and falls back to this month for invalid custom dates', function () {
    actingAs(User::factory()->create());

    $page = Livewire::test(HomePage::class)->set('period', 'last_month');

    expect($page->instance()->range()['start'])->toBe(today()->subMonthNoOverflow()->startOfMonth()->toDateString());

    $page->set('period', 'custom')->set('from', '2026-03-10')->set('to', '2026-03-01');

    expect($page->instance()->range()['start'])->toBe(today()->startOfMonth()->toDateString());
});

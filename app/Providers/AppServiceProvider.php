<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\ResolveInvitationsOnAuth;
use App\Models\Project;
use App\Models\Task;
use App\Policies\ProjectPolicy;
use App\Policies\TaskPolicy;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureScrambleApiAuthentication();

        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(Project::class, ProjectPolicy::class);

        Event::listen(Verified::class, ResolveInvitationsOnAuth::class);
    }

    private function configureScrambleApiAuthentication(): void
    {
        Scramble::afterOpenApiGenerated(function ($openApi): void {
            $openApi->secure(
                SecurityScheme::apiKey('header', 'X-API-Key')
                    ->setDescription('Use the API key generated from your user dropdown to authorize Scramble requests.')
            );
        });
    }
}

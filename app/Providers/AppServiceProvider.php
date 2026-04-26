<?php

declare(strict_types=1);

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
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

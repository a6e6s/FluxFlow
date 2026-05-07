<?php

use App\Http\Middleware\AuthenticateApiKey;
use App\Http\Middleware\SetLocale;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetLocale::class,
        ]);

        $middleware->api(prepend: [
            AuthenticateApiKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ModelNotFoundException $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $message = match ($exception->getModel()) {
                Project::class => 'Project not found.',
                Task::class => 'Task not found.',
                default => 'Resource not found.',
            };

            return response()->json([
                'message' => $message,
            ], 404);
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            $previous = $exception->getPrevious();

            $message = match (true) {
                $previous instanceof ModelNotFoundException && $previous->getModel() === Project::class => 'Project not found.',
                $previous instanceof ModelNotFoundException && $previous->getModel() === Task::class => 'Task not found.',
                default => 'Resource not found.',
            };

            return response()->json([
                'message' => $message,
            ], 404);
        });
    })->create();

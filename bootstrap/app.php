<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
        'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        'cms.access' => \App\Http\Middleware\EnsureUserCanAccessCms::class,
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) {
            $status = $exception->getStatusCode();

            if (! in_array($status, [403, 404, 405], true)) {
                return null;
            }

            $content = match ($status) {
                403 => [
                    'title' => 'Access denied',
                    'message' => 'You do not have permission to access this page.',
                ],
                404 => [
                    'title' => 'Page not found',
                    'message' => 'The page you are looking for does not exist or may have moved.',
                ],
                405 => [
                    'title' => 'Method not allowed',
                    'message' => 'That page exists, but it does not support this type of request.',
                ],
            };

            $primaryActionUrl = $request->user()
                ? route('dashboard')
                : route('login');

            $primaryActionLabel = $request->user()
                ? 'Go to dashboard'
                : 'Go to login';

            return response()->view('errors.http-status', [
                'code' => (string) $status,
                'title' => $content['title'],
                'message' => $content['message'],
                'primaryActionUrl' => $primaryActionUrl,
                'primaryActionLabel' => $primaryActionLabel,
                'secondaryActionUrl' => null,
                'secondaryActionLabel' => null,
            ], $status);
        });
    })->create();

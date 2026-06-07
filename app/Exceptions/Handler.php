<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Psr\Log\LoggerInterface;
use Throwable;
use App\Services\NotificationService;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    public function register(): void
    {
        // renderable callbacks can be registered here if needed
    }

    public function report(Throwable $exception): void
    {
        parent::report($exception);

        try {
            $status = 500;
            if ($exception instanceof HttpExceptionInterface) {
                $status = $exception->getStatusCode();
            }

            if ($status === 500) {
                // send admin notification about server error
                if (app()->bound(NotificationService::class)) {
                    $message = $exception->getMessage() ?: 'Internal Server Error';
                    app(NotificationService::class)->notifyAdmins(
                        'error',
                        '🚨 Kesalahan Server (500)',
                        "Terjadi kesalahan server: {$message}"
                    );
                }
            }
        } catch (Throwable $e) {
            // avoid infinite loops; ensure reporting continues
            // swallow errors here
        }
    }

    public function render($request, Throwable $exception)
    {
        $status = 500;
        if ($exception instanceof HttpExceptionInterface) {
            $status = $exception->getStatusCode();
        }

        // Map common status codes to our views
        $view = match ($status) {
            401 => 'errors.401',
            403 => 'errors.403',
            404 => 'errors.404',
            419 => 'errors.419',
            422 => 'errors.422',
            default => 'errors.500',
        };

        if (view()->exists($view)) {
            return response()->view($view, ['exception' => $exception], $status);
        }

        return parent::render($request, $exception);
    }
}

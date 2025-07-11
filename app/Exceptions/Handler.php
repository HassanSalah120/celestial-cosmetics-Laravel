<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Handle specific exceptions with custom views
        $this->renderable(function (TokenMismatchException $e) {
            return response()->view('errors.419', [], 419);
        });

        $this->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                ], 422);
            }
        
            return response()->view('errors.422', ['exception' => $e], 422);
        });

        $this->renderable(function (MethodNotAllowedHttpException $e) {
            return response()->view('errors.405', ['exception' => $e], 405);
        });

        $this->renderable(function (NotFoundHttpException $e) {
            return response()->view('errors.404', [], 404);
        });

        $this->renderable(function (TooManyRequestsHttpException $e) {
            return response()->view('errors.429', [], 429);
        });

        $this->renderable(function (HttpException $e) {
            $statusCode = $e->getStatusCode();
            
            // Check if we have a specific error view for this status code
            if (view()->exists("errors.{$statusCode}")) {
                return response()->view("errors.{$statusCode}", ['exception' => $e], $statusCode);
            }

            // Fallback to generic error view
            return response()->view('errors.generic', [
                'errorCode' => $statusCode,
                'errorMessage' => $e->getMessage() ?: 'An error occurred',
                'exception' => $e
            ], $statusCode);
        });

        // Catch-all for other exceptions
        $this->renderable(function (Throwable $e) {
            if (app()->environment('production')) {
                $statusCode = 500;
                
                return response()->view('errors.500', [
                    'errorMessage' => 'Server Error',
                    'exception' => $e
                ], $statusCode);
            }
        });
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? response()->json(['message' => 'Unauthenticated.'], 401)
            : response()->view('errors.401', [], 401);
    }
} 
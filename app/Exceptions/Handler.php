<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return response()->json([
                'error' => 'Validation Error',
                'message' => $exception->validator->errors()->first()
            ], 422);
        }

        // Custom response for NotFoundHttpException (404 error)
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'error' => 'Resource not found',
                'message' => 'The requested URL was not found on this server.',
            ], 404);
        }

        // Custom response for MethodNotAllowedHttpException
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'error' => 'Method not allowed',
                'message' => 'The HTTP method is not allowed for the requested route.',
            ], 405);
        }

        // You can customize other exceptions similarly

        return parent::render($request, $exception);
    }
}

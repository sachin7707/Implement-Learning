<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // handles json calls, if found in ACCEPT header
        if ($request->wantsJson()) {
            $responseCode = 400;
            $response = [
                'message' => (string)$exception->getMessage()
            ];

            if ($exception instanceof HttpException) {
                $response['message'] = Response::$statusTexts[$exception->getStatusCode()];
                $responseCode = $exception->getStatusCode();
            } elseif ($exception instanceof ModelNotFoundException) {
                $response['message'] = Response::$statusTexts[Response::HTTP_NOT_FOUND];
                $responseCode = Response::HTTP_NOT_FOUND;
            }

            // if we are debugging, add exception information
            if ($this->isDebugMode()) {
                $response['debug'] = [
                    'exception' => get_class($exception),
                    'trace' => $exception->getTraceAsString()
                ];
            }

            return response()->json($response, $responseCode);
        }

        return parent::render($request, $exception);
    }

    /**
     * Checks if we are in debug mode
     * @return bool
     */
    private function isDebugMode()
    {
        return (bool)env('APP_DEBUG');
    }
}

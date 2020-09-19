<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception) : void
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception $exception
     * @return JsonResponse|Response
     * @throws Exception
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json([
                'message' => __('error.404.message')
            ], 404);
        }

        // Handle json error
        if ($request->wantsJson()) {
            return $this->handleApiException($request, $exception);
        }

        // Convert all non-http exceptions to a proper 500 http exception
        // if we don't do this exceptions are shown as a default template
        // instead of our own view in resources/views/errors/500.blade.php
        if ($this->shouldReport($exception) && !$this->isHttpException($exception) && !config('app.debug')) {
            $exception = new HttpException(500, 'Whoops!');
        }

        return parent::render($request, $exception);
    }

    /**
     * @param $request
     * @param Exception $exception
     * @return JsonResponse
     */
    private function handleApiException($request, Exception $exception): JsonResponse
    {
        $exception = $this->prepareException($exception);

        if ($exception instanceof HttpResponseException) {
            $exception = $exception->getResponse();
        }

        if ($exception instanceof AuthenticationException) {
            $exception = $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof ValidationException) {
            $exception = $this->convertValidationExceptionToResponse($exception, $request);
        }

        return $this->customApiResponse($exception);
    }

    /**
     * @param $exception
     * @return JsonResponse
     */
    private function customApiResponse($exception): JsonResponse
    {
        $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
        $response = [];

        switch ($statusCode) {
            case 401:
                $response['message'] = __('error.401.message');
                break;
            case 403:
                $response['message'] = __('error.403.message');
                break;
            case 404:
            case 503:
                $response['message'] = $exception->getMessage();
                break;
            case 405:
                $response['message'] = __('error.405.message');
                break;
            case 422:
                $response = [
                    'message' => $exception->original['errors'][array_key_first($exception->original['errors'])][0]
                ];
                break;
            case 500:
                $response['message'] = config('app.debug') ? $exception->getMessage() : __('error.500.message');

                if (config('app.debug')) {
                    $response['trace'] = $exception->getTrace();
                    $response['code'] = $exception->getCode();
                }
                break;
            default:
                $response['message'] = $statusCode === 500 ? __('error.500.message') : $exception->getMessage();
                break;
        }

        return response()->json($response, $statusCode);
    }
}

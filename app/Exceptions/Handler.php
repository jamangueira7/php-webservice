<?php

namespace App\Exceptions;

use Exception;
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
        $arrayException = [
            HttpException::class,
            ModelNotFoundException::class,
            ValidationException::class
        ];

        if(in_array(get_class($exception),$arrayException)){

            $response = parent::render($request, $exception);
            $arrayError = [
                'status_code' => $response->getStatusCode(),
                'erro_code' => 5557,
                'message' => $exception->getMessage(),
                'about_error' => 'algum link'
            ];
            if($exception instanceof ValidationException){
                $arrayError['fields'] = $exception->validator->getMessageBag()->toArray();
            }
            return son_response()->make($arrayError, $response->getStatusCode());
        }
        return parent::render($request, $exception);
    }
}

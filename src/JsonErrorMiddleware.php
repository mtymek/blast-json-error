<?php

namespace Blast\JsonError;

use ErrorException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Zend\Diactoros\Response\JsonResponse;

class JsonErrorMiddleware
{
    use GetStatusCodeTrait;

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            if (! (error_reporting() & $errno)) {
                // Error is not in mask
                return;
            }
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        });
        $throwable = null;

        try {
            $response = $next($request, $response);
            return $response;
        } catch (Throwable $throwable) {
        }

        restore_error_handler();

        return new JsonResponse(
            "An error has occurred.",
            $this->getStatusCode($throwable, $response)
        );
    }
}

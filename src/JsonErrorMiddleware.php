<?php

declare(strict_types=1);

namespace Blast\JsonError;

use ErrorException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use Laminas\Diactoros\Response\JsonResponse;

class JsonErrorMiddleware implements MiddlewareInterface
{
    use GetStatusCodeTrait;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
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
            $response = $handler->handle($request);
            return $response;
        } catch (Throwable $throwable) {
        }

        restore_error_handler();

        return new JsonResponse(
            "An error has occurred.",
            $this->getStatusCode($throwable)
        );
    }
}

<?php

declare(strict_types=1);

namespace BlastTest\JsonError;

use Blast\JsonError\JsonErrorMiddleware;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\ServerRequest;

class JsonErrorMiddlewareTest extends TestCase
{
    public function testReturnsJsonWithErrorMessage()
    {
        $middleware = new JsonErrorMiddleware();
        $response = $middleware->process(new ServerRequest(), $this->mockRequestHandlerThrowingException());
        $this->assertEquals('"An error has occurred."', $response->getBody()->__toString());
    }

    private function mockRequestHandlerThrowingException()
    {
        return new class() implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                throw new Exception("error");
            }
        };
    }
}

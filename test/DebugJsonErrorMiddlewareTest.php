<?php

declare(strict_types=1);

namespace Blast\Test\JsonError;

use Blast\JsonError\DebugJsonErrorMiddleware;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\ServerRequest;

class DebugJsonErrorMiddlewareTest extends TestCase
{
    public function testReturnsJsonWithExceptionInformation()
    {
        $middleware = new DebugJsonErrorMiddleware(true, dirname(__DIR__));
        $response = $middleware->process(new ServerRequest(), $this->mockRequestHandlerThrowingException());
        $json = json_decode($response->getBody()->__toString(), true);
        $this->assertEquals('test/DebugJsonErrorMiddlewareTest.php:30', $json['error']['file']);
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

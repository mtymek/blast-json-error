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

class GetStatusCodeTraitTest extends TestCase
{
    public function testReadsStatusCodeFromException()
    {
        $middleware = new JsonErrorMiddleware();
        $response = $middleware->process(new ServerRequest(), $this->mockRequestHandlerThrowingException(502));
        $this->assertEquals(502, $response->getStatusCode());
    }

    public function testIgnoresExceptionCodeIfItDoestNotMatchHttpErrorCode()
    {
        $middleware = new JsonErrorMiddleware();
        $response = $middleware->process(new ServerRequest(), $this->mockRequestHandlerThrowingException(1000));
        $this->assertEquals(500, $response->getStatusCode());
    }

    private function mockRequestHandlerThrowingException(int $code)
    {
        return new class($code) implements RequestHandlerInterface {
            private $code;

            public function __construct($code)
            {
                $this->code = $code;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                throw new Exception("error", $this->code);
            }
        };
    }
}

<?php

namespace Blast\Test\JsonError;

use Blast\JsonError\JsonErrorMiddleware;
use Exception;
use PHPUnit_Framework_TestCase;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class GetStatusCodeTraitTest extends PHPUnit_Framework_TestCase
{
    public function testReadsStatusCodeFromException()
    {
        $middleware = new JsonErrorMiddleware();
        $response = $middleware(new ServerRequest(), new Response(), function () {
            throw new Exception("error", 502);
        });
        $this->assertEquals(502, $response->getStatusCode());
    }

    public function testIgnoresExceptionCodeIfItDoestNotMatchHttpErrorCode()
    {
        $middleware = new JsonErrorMiddleware();
        $response = $middleware(new ServerRequest(), new Response(), function () {
            throw new Exception("error", 1000);
        });
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testReadsStatusCodeFromPreviousResponse()
    {
        $middleware = new JsonErrorMiddleware();
        $originalResponse = (new Response())->withStatus(522);
        $response = $middleware(new ServerRequest(), $originalResponse, function () {
            throw new Exception();
        });
        $this->assertEquals(522, $response->getStatusCode());
    }

    public function testIgnoresPreviousResponseCodeIfItDoestNotMatchHttpErrorCode()
    {
        $middleware = new JsonErrorMiddleware();
        $originalResponse = (new Response())->withStatus(200);
        $response = $middleware(new ServerRequest(), $originalResponse, function () {
            throw new Exception();
        });
        $this->assertEquals(500, $response->getStatusCode());
    }
}

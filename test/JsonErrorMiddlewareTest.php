<?php

namespace Blast\Test\JsonError;

use Blast\JsonError\JsonErrorMiddleware;
use Exception;
use PHPUnit_Framework_TestCase;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class JsonErrorMiddlewareTest extends PHPUnit_Framework_TestCase
{
    public function testReturnsJsonWithErrorMessage()
    {
        $middleware = new JsonErrorMiddleware();
        $response = $middleware(new Exception(), new ServerRequest(), new Response(), function () {
        });
        $this->assertEquals('"An error has occurred."', $response->getBody()->__toString());
    }
}

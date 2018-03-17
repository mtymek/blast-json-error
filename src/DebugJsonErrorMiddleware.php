<?php

declare(strict_types=1);

namespace Blast\JsonError;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use Whoops\Exception\Inspector;
use Zend\Diactoros\Response\JsonResponse;

class DebugJsonErrorMiddleware implements MiddlewareInterface
{
    use GetStatusCodeTrait;

    /** @var string */
    private $baseDir;

    private $stripBaseDir = false;

    public function __construct($stripBaseDir = false, $baseDir = null)
    {
        if ($stripBaseDir) {
            if (null == $baseDir) {
                $this->baseDir = getcwd();
            } else {
                $this->baseDir = $baseDir;
            }
        }
        $this->stripBaseDir = $stripBaseDir;
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $response = $handler->handle($request);
            return $response;
        } catch (Throwable $throwable) {
        }

        $data = [
            'type'    => get_class($throwable),
            'message' => $throwable->getMessage(),
            'file'    => $this->formatDir($throwable->getFile()) . ':' . $throwable->getLine()
        ];

        $inspector = new Inspector($throwable);

        $frameData = [];
        foreach ($inspector->getFrames() as $frame) {
            $frameData[] = [
                'file'     => $this->formatDir($frame->getFile()) . ':' . $frame->getLine(),
                'method'   => $frame->getClass() . '::' . $frame->getFunction()
            ];
        }
        $data['trace'] = $frameData;

        return new JsonResponse(
            ['error' => $data],
            $this->getStatusCode($throwable)
        );
    }

    private function formatDir($dir)
    {
        if (!$this->stripBaseDir) {
            return $dir;
        }
        if (strpos($dir, $this->baseDir) !== 0) {
            return $dir;
        }

        return ltrim(str_replace($this->baseDir, '', $dir), '/');
    }
}

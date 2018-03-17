<?php

declare(strict_types=1);

namespace Blast\JsonError;

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => [
                'factories' => [
                    DebugJsonErrorMiddleware::class => DebugJsonErrorMiddlewareFactory::class,
                ],
            ],
        ];
    }
}

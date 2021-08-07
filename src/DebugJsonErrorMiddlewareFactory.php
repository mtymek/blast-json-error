<?php

declare(strict_types=1);

namespace Blast\JsonError;

use Psr\Container\ContainerInterface;

class DebugJsonErrorMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): DebugJsonErrorMiddleware
    {
        if (!$container->has('config')) {
            return new DebugJsonErrorMiddleware();
        }
        $config = $container->get('config');
        $stripBaseDir = false;
        $baseDir = null;
        if (isset($config['blast_json_error'])) {
            if (isset($config['blast_json_error']['strip_base_dir'])) {
                $stripBaseDir = (bool)$config['blast_json_error']['strip_base_dir'];
            }
            if (isset($config['blast_json_error']['base_dir'])) {
                $baseDir = (string)$config['blast_json_error']['base_dir'];
            }
        }
        return new DebugJsonErrorMiddleware($stripBaseDir, $baseDir);
    }
}

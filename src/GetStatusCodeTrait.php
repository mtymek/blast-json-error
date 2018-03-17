<?php

declare(strict_types=1);

namespace Blast\JsonError;

use Exception;

trait GetStatusCodeTrait
{
    /**
     * Determine status code from an error and/or response.
     *
     * If the error is an exception with a code between 400 and 599, returns
     * the exception code.
     *
     * Otherwise, returns HTTP 500.
     *
     * @param mixed $error
     * @return int
     */
    private function getStatusCode($error)
    {
        if ($error instanceof Exception
            && ($error->getCode() >= 400 && $error->getCode() < 600)
        ) {
            return $error->getCode();
        }

        return 500;
    }
}

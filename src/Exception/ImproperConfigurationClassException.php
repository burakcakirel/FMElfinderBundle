<?php

declare(strict_types=1);

namespace FM\ElfinderBundle\Exception;

use Exception;
use Throwable;

class ImproperConfigurationClassException extends Exception
{
    public function __construct(int $code = 0, Throwable $previous = null)
    {
        parent::__construct('Configurator class must implement ElFinderConfigurationProviderInterface', $code, $previous);
    }
}

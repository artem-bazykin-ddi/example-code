<?php
declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Sentry;
use Throwable;

class SentryException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        Sentry::captureException($this);
    }
}

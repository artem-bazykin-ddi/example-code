<?php

namespace App\Exceptions;

use App\Models\Activity;
use LogicException;
use Throwable;

class InvalidActivityException extends LogicException
{
    /**
     * InvalidActivityException constructor.
     *
     * @param Activity $activity
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(Activity $activity, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

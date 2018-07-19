<?php

namespace MyFw\exceptions;


use Throwable;

class RouteException extends \RuntimeException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
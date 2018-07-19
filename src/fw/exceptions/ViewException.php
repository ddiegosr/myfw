<?php
/**
 * Created by PhpStorm.
 * User: diego
 * Date: 19/07/18
 * Time: 02:48
 */

namespace MyFw\exceptions;


use Throwable;

class ViewException extends \RuntimeException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}